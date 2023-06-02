<?php

namespace App\Http\Controllers\billing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Billing\CreateInvoiceRequest;
use App\Models\UserBillingInfo;
use App\Models\UserConnectionInfo;
use App\Models\Nikrotik;
use App\Services\ConnectionService;

class Billing extends Controller
{
    public function createInvoice(CreateInvoiceRequest $request)
    {
        try {
            DB::beginTransaction();
            // $package = $this->userPackageInfo($request->user_id);
            // update package 
            $usr_bill = UserBillingInfo::where('user_id', $request->user_id)->first();
            if (empty($usr_bill)) return error_message('User package info not found');
            $usr_bill->update([
                'package_id' => $usr_bill->purches_package_id,
            ]);
            // update package 
            if ($usr_bill->monthly_bill < $request->received_amount) $status = 'Over Paid';
            else if ($usr_bill->monthly_bill > $request->received_amount) $status = 'Partially Paid';
            else if ($usr_bill->monthly_bill = $request->received_amount) $status = 'Fully Paid';
            else $status = '';
            $invoice_data = [
                'user_id' => $request->user_id,
                'invoice_no' => "INV-{$request->user_id}-" . date('m-d-H'),
                'package_id' => $usr_bill->package_id,
                'zone_id' => $usr_bill->zone_id,
                // 'expire_date' => now()->addDays(30),
                'expire_date' => Carbon::now(),
                'amount' => $request->amount,
                'received_amount' => $request->received_amount,
                'advanced_amount' => $usr_bill->monthly_bill - $request->received_amount,
                'due_amount' => $usr_bill->monthly_bill > $request->received_amount,
                'last_payment_amount' => $request->received_amount,
                'last_payment_date' => now(),
                'status' => $status
            ];
            $invoice = Invoice::create($invoice_data);
            $user_con_info =  UserConnectionInfo::where('user_id', $request->user_id)->first();

            $mikrotik = Mikrotik::where('id', $user_con_info->mikrotik_id)->first();
            if (empty($mikrotik)) return error_message('Mikrotik not found');
            $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
            $connection->activeDisconnectedUser($request->user_id, $user_con_info->username);
            // update user expire date 
            if (strtotime($user_con_info->expire_date) > strtotime(Carbon::now()->format('d-m-Y H:i A'))  && $invoice->comment == 'new_user') {
                $expireDate = Carbon::now()->addMonth()->format('d-m-Y H:i A');
            } elseif (strtotime($user_con_info->expire_date) > strtotime(Carbon::now()->format('d-m-Y H:i A'))  && $invoice->comment == 'date_expire') {
                $expireDate =  Carbon::parse($user_con_info->expire_date)->addMonth()->format('d-m-Y H:i A');
            } elseif (strtotime($user_con_info->expire_date) > strtotime(Carbon::now()->format('d-m-Y H:i A'))  && $request->invoice_for == 'Monthly Bill') {
                $expireDate =  Carbon::parse($user_con_info->expire_date)->addMonth()->format('d-m-Y H:i A');
            } else {
                $expireDate =  Carbon::now()->addMonth()->format('d-m-Y H:i A');
            }

            $user_con_info->update(['expire_date' => $expireDate]);
            // update user expire date 

            // if (!empty($request->amount)) {
            //     SettingController::account_calculate('dabit_balance', $request->received_amount);
            // }


            if ($request->invoice_for == 'Add Balance') {
                $usr_bill = UserBillingInfo::where('user_id', $request->user_id)->first();
                if ($request->amount < $request->received_amount) {;
                    $balance =  $request->received_amount - $request->amount;
                };
                $usr_bill->update([
                    'balance' => $usr_bill->balance += $balance,
                ]);
                CustomerBalanceHistory::create([
                    'users_id' => $request->user_id,
                    'balance'  => $balance,
                    'update_Reasons' => 'Add New Balance By Invoice',
                    'admin_id' => Auth::user()->id,
                ]);
            }

            DB::commit();
            return success_message("Invoice Created Successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            info(json_encode($e->getMessage()));
            return error_message('Database Exception Error', $e->getMessage(), $e->getCode());
        }
    }
}
