<?php

namespace App\Http\Controllers\customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\SubZone;
use App\Models\Package;
use App\Models\Mikrotik;
use App\Models\PppUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Customer as CustomerModel;
use Illuminate\Support\Facades\DB;
use App\Services\ConnectionService;
use App\Services\User\UserConnectionService;
use App\Models\Invoice;
use Carbon\Carbon;
use \Datetime;

class Customer extends Controller
{
    private $user_connection_service;

    public function __construct(
        UserConnectionService $user_connection_service,
    ) {

        $this->user_connection_service = $user_connection_service;
    }

    public function addCustomer(){

        $zones = Zone::all();
        $subzones = SubZone::all();
        $packages = Package::all();
        $mikrotiks = Mikrotik::all();
        return view('content.user.add-user', compact('zones', 'packages', 'mikrotiks', 'subzones'));
    }

    public function storeCustomer(Request $request){

        $request->validate([
            'name' => 'required',
            'gender' => 'required',
            'national_id' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'dob' => 'required',
            'f_name' => 'required',
            'm_name' => 'required',
            'address' => 'required',
            'zone_id' => 'required',
            'sub_zone_id' => 'required',
            'reg_date' => 'required',
            'conn_date' => 'required',
            'package_id' => 'required',
            'bill' => 'required',
            'discount' => 'required',
            'mikrotik_id' => 'required',
            'username' => 'required',
            'password' => 'required'
        ]);

        CustomerModel::create([
            'full_name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'national_id' => $request->national_id,
            'phone' => $request->phone,
            'date_of_birth' => $request->dob,
            'father_name' => $request->f_name,
            'mother_name' => $request->m_name,
            'address' => $request->address,
            'zone_id' => $request->zone_id,
            'sub_zone_id' => $request->sub_zone_id,
            'registration_date' => $request->reg_date,
            'connection_date' => $request->conn_date,
            'package_id' => $request->package_id,
            'bill' => $request->bill,
            'discount' => $request->discount,
            'mikrotik_id' => $request->mikrotik_id,
            'username' => $request->username,
            'password' => $request->password,
            'pending' => true
        ]);

        return redirect()->back();
    }

    public function getPackageDetails($id){

        $package = Package::find($id);
        $bill = $package->price;
        return response()->json(['bill' => $bill]);
    }

    public function viewCustomer(){
        $users = CustomerModel::where('pending', false)->get();
        return view('content.user.view-user', compact('users'));
    }

    public function pendingCustomer(){

        $users = CustomerModel::where('pending', true)->where('id_in_mkt', null)->get();
        $mikrotik_users = PppUser::all();
        return view('content.user.pending-user', compact('users', 'mikrotik_users'));
    }

    public function approveCustomer(Request $request, $id){

        $request->validate([
            'received_amount' => 'required',
            'paid_by' => 'required',
            'status' => 'required'
        ]);
        $query_data = '';
        try {
            $user = CustomerModel::where('id', $id)->first();
            $package = $user->package;
            $now = new DateTime();
            $expire_date = strtotime($user->connection_date) + $package->validdays;
            $expire_date_formatted = gmdate("Y-m-d", $expire_date);

            if($user->discount != null){
                $amount = $user->bill - $user->discount;
            }
            else{
                $amount = $user->bill;
            }

            $mikrotik = Mikrotik::where('id', $user->mikrotik_id)->first();
            if (empty($mikrotik)) return error_message('Mikrotik not found');
            //create client
            $connection = new ConnectionService($mikrotik->host, $mikrotik->username, $mikrotik->password, $mikrotik->port);
            $query_data = $connection->addUserToMikrotik($user);
            if (gettype($query_data) == 'string') return error_message($query_data);
            $this->query_data = $query_data[0]['expire_date'];
            $this->user_connection_service->create($user, $this->query_data);

            $invoice = Invoice::create([
                'user_id' => $user->id,
                'invoice_no' => "INV-{$user->id}-" . date('m-d-H'),
                'invoice_for' => 'new_user',
                'package_id' => $user->package_id,
                'zone_id' => $user->zone_id,
                'sub_zone_id' => 1,
                'expire_date' => $expire_date_formatted,
                'amount' => $amount,
                'received_amount' => $request->received_amount,
                'paid_by' => $request->paid_by,
                'transaction_id' => $request->transaction_id,
                'comment' => 'New User'
            ]);

            if($amount > $request->received_amount){
                $due = $amount - $request->received_amount;
                $invoice->due_amount = $due;
                $invoice->status = 'due';
                $invoice->save();

                $user->pending = false;
                $user->billing_date = $expire_date_formatted;
                $user->wallet = $request->received_amount - $amount; 
                $user->save();
            }
            else if($amount < $request->received_amount){
                $advanced = $request->received_amount - $amount;
                $invoice->advanced_amount = $advanced;
                $invoice->status = 'over_paid';
                $invoice->save();

                $user->pending = false;
                $user->billing_date = $expire_date_formatted;
                $user->wallet = $request->received_amount - $amount; 
                $user->save();
            }
            else{
                $user->pending = false;
                $user->billing_date = $expire_date_formatted;
                $user->save();
            }

            DB::commit();
            // return success_message("User Create Successfully with Mikrotik");
            return redirect()->back();
        } catch (Exception $exception) {
            DB::rollBack();
            info($exception->getMessage());
            // return error_message('Something went wrong!', $exception->getMessage(), $exception->getCode());
            return 0;
        }
    }

    public function editCustomer($id){

        $user = CustomerModel::where('id', $id)->first();
        $zones = Zone::all();
        $packages = Package::all();
        $mikrotiks = Mikrotik::all();
        return view('content.user.edit-user', compact('user', 'zones', 'packages', 'mikrotiks'));
    }

    public function updateCustomer(Request $request, $id){

        $request->validate([
            'name' => 'required',
            'gender' => 'required',
            'national_id' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'dob' => 'required',
            'f_name' => 'required',
            'm_name' => 'required',
            'address' => 'required',
            'zone_id' => 'required',
            'reg_date' => 'required',
            'conn_date' => 'required',
            'package_id' => 'required',
            'bill' => 'required',
            'discount' => 'required',
            'mikrotik_id' => 'required',
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = CustomerModel::find($id);
        $user->update([
            'full_name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'national_id' => $request->national_id,
            'phone' => $request->phone,
            'date_of_birth' => $request->dob,
            'father_name' => $request->f_name,
            'mother_name' => $request->m_name,
            'address' => $request->address,
            'zone_id' => $request->zone_id,
            'registration_date' => $request->reg_date,
            'connection_date' => $request->conn_date,
            'package_id' => $request->package_id,
            'bill' => $request->bill,
            'discount' => $request->discount,
            'mikrotik_id' => $request->mikrotik_id,
            'username' => $request->username,
            'password' => $request->password,
            'pending' => true
        ]);

        return redirect()->back();

    }

    public function editMikrotikCustomer($id){

        $user = PppUser::where('id', $id)->first();
        $customer_package = Package::where('name', $user->profile)->first();
        $zones = Zone::all();
        $packages = Package::all();
        $mikrotiks = Mikrotik::all();
        return view('content.user.edit-mikrotik-user', compact('user', 'zones', 'packages', 'mikrotiks', 'customer_package'));
    }

    public function storeMikrotikCustomer(Request $request){

        $request->validate([
            'id_in_mkt' => 'required',
            'name' => 'required',
            'gender' => 'required',
            'national_id' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'dob' => 'required',
            'f_name' => 'required',
            'm_name' => 'required',
            'address' => 'required',
            'zone_id' => 'required',
            'package_id' => 'required',
            'bill' => 'required',
            'discount' => 'required',
            'billing_date' => 'required',
            'mikrotik_id' => 'required',
            'username' => 'required',
            'password' => 'required'
        ]);

        CustomerModel::create([
            'id_in_mkt' => $request->id_in_mkt,
            'full_name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'national_id' => $request->national_id,
            'phone' => $request->phone,
            'date_of_birth' => $request->dob,
            'father_name' => $request->f_name,
            'mother_name' => $request->m_name,
            'address' => $request->address,
            'zone_id' => $request->zone_id,
            'registration_date' => $request->reg_date,
            'connection_date' => $request->conn_date,
            'billing_date' => $request->billing_date,
            'package_id' => $request->package_id,
            'bill' => $request->bill,
            'discount' => $request->discount,
            'mikrotik_id' => $request->mikrotik_id,
            'username' => $request->username,
            'password' => $request->password,
            'pending' => false
        ]);

        $ppp_user = PppUser::where('id_in_mkt',  $request->id_in_mkt)->first();
        $ppp_user->added_in_customers_table = true;
        $ppp_user->save();

        return redirect()->back();
    }

    public function storeInvoice(Request $request){

        $request->validate([
            'user_id' => 'required',
            'invoice_for' => 'required',
            'expire_date' => 'required',
            'amount' => 'required',
            'received_amount' => 'required',
            'paid_by' => 'required',
        ]);

        Invoice::create([
            'user_id' => $request->user_id,
            'invoice_no' => "INV-{$request->user_id}-" . date('m-d-H'),
            'invoice_for' => $request->invoice_for,
            'expire_date' => $request->expire_date,
            'amount' => $request->amount,
            'received_amount' => $request->received_amount,
            'status' => $request->status,
            'paid_by' => $request->paid_by,
            'transaction_id' => $request->transaction_id,
        ]);

        return redirect()->back();
    }

    public function disconnectExpiredCustomer(Request $request){

        $customers = CustomerModel::where('pending', false)->get();
        $today = now()->format('Y-m-d');
        for($i=0; $i<count($customers); $i++){
            if($customers[$i]->billing_date < $today){
                $connection = new ConnectionService($customers[$i]->mikrotik->host, $customers[$i]->mikrotik->username, $customers[$i]->mikrotik->password, $customers[$i]->mikrotik->port);
                $expired_package = Package::where('name', 'Expired')->where('mikrotik_id', $customers[$i]->mikrotik_id)->first();
                $connection->disconnectUserProfile($customers[$i]->id, $customers[$i]->username, $expired_package->name);
            }
        }
        return response()->json(['request received']);
    }
}
