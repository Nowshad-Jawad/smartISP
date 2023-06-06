<?php

namespace App\Http\Controllers\account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccountCategory;
use App\Models\BillCollection;
use App\Models\Customer;
use App\Models\Manager;
use App\Models\DailyIncome;
use App\Models\DailyExpense;
use App\Models\Invoice;
use \Datetime;

class Account extends Controller
{
    public function viewCategory(){
        $categories = AccountCategory::all();
        return view('content.account.category.view-category', compact('categories'));
    }

    public function storeCategory(Request $request){
        $request->validate([
            'name' => 'required',
            'type' => 'required'
        ]);

        AccountCategory::create([
            'name' => $request->name,
            'type' => $request->type,
            'status' => true
        ]);

        return redirect()->back();
    }

    public function updateCategory(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'type' => 'required'
        ]);

        $category = AccountCategory::find($id);

        if($request->status == 'on'){
            $status = true;
        }
        else{
            $status = false;
        }

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'status' => $status
        ]);

        return redirect()->back();
    }

    public function viewBillCollection(){
        $collections = BillCollection::all();
        $customers = Customer::all();
        $managers = Manager::all();
        return view('content.account.bill-collection.view-bill-collection', compact('collections', 'customers', 'managers'));
    }

    public function customerDetails(Request $request){

        $selected_customer = Customer::where('id', $request->customer)->first();
        return response()->json(['customer' => $selected_customer]);
    }

    public function storeBillCollection(Request $request){
        $request->validate([
            'customer' => 'required',
            'customer_name' => 'required',
            'method' => 'required',
            'monthly_bill' => 'required',
            'received' => 'required',
            'manager' => 'required',
            'issue_date' => 'required',
            'note' => 'required'
        ]);

        $bill = BillCollection::create([
            'customer_name' => $request->customer_name,
            'customer_id' => $request->customer,
            'invoice_no' => time(),
            'method' => $request->method,
            'monthly_bill' => $request->monthly_bill,
            'received_amount' => $request->received,
            'manager_id' => $request->manager,
            'issue_date' => $request->issue_date,
            'note' => $request->note
        ]);

        $customer = Customer::where('id', $bill->customer_id)->first();
        $package = $customer->package()->first();

        $now = new DateTime();
        $expire_date = strtotime($customer->billing_date) + $package->validdays;
        $expire_date_formatted = gmdate("Y-m-d", $expire_date);

        $invoice = Invoice::create([
            'user_id' => $bill->customer_id,
            'invoice_no' => $bill->invoice_no,
            'invoice_for' => 'monthly_bill',
            'package_id' => $bill->customer->package_id,
            'zone_id' => $bill->customer->zone_id,
            'sub_zone_id' => 1,
            'expire_date' => $expire_date_formatted,
            'amount' => $bill->monthly_bill,
            'received_amount' => $bill->received_amount,
            'paid_by' => $bill->method,
            'transaction_id' => $bill->transaction_id,
            'comment' => 'invoice for monthly bill'
        ]);

        if($bill->monthly_bill > $bill->received_amount){
            $due = $bill->monthly_bill - $bill->received_amount;
            $invoice->due_amount = $due;
            $invoice->status = 'due';
            $invoice->save();
        }
        else if($bill->monthly_bill < $bill->received_amount){
            $advanced = $bill->received_amount - $bill->monthly_bill;
            $invoice->advanced_amount = $advance;
            $invoice->status = 'over_paid';
            $invoice->save();
        }
        else{
            $invoice->status = 'paid';
            $invoice->save(); 
        }

        if($request->add_wallet_balance == 'on'){
            $customer->wallet = ($customer->wallet - $bill->received_amount);
            $customer->billing_date = $expire_date_formatted;
            $customer->save();
        }
        else{
            $customer->billing_date = $expire_date_formatted;
            $customer->save();
        }

        return redirect()->back();
    }

    public function viewDailyIncome(){
        $incomes = DailyIncome::all();
        $categories = AccountCategory::where('type', 'Income')->get();
        return view('content.account.daily-income.view-daily-income', compact('incomes', 'categories'));
    }

    public function storeDailyIncome(Request $request){
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'amount' => 'required',
            'method' => 'required',
            'date' => 'required',
            'description' => 'required'
        ]);

        $creator = 1;
        $vouchar = time();

        DailyIncome::create([
            'service_name' => $request->name,
            'category_id' => $request->category,
            'amount' => $request->amount,
            'method' => $request->method,
            'date' => $request->date,
            'description' => $request->description,
            'manager_id' => 1,
            'vouchar_no' => $vouchar,
            'transaction_id' => $request->transaction
        ]);

        return redirect()->back();

    }

    public function viewDailyExpense(){
        $expenses = DailyExpense::all();
        $categories = AccountCategory::where('type', 'Expense')->get();
        return view('content.account.daily-expense.view-daily-expense', compact('expenses', 'categories'));
    }

    public function storeDailyExpense(Request $request){
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'amount' => 'required',
            'method' => 'required',
            'date' => 'required',
            'description' => 'required'
        ]);

        $creator = 1;
        $vouchar = time();

        DailyExpense::create([
            'expense_claimant' => $request->name,
            'category_id' => $request->category,
            'amount' => $request->amount,
            'method' => $request->method,
            'date' => $request->date,
            'description' => $request->description,
            'manager_id' => 1,
            'vouchar_no' => $vouchar,
            'transaction_id' => $request->transaction
        ]);

        return redirect()->back();
    }
}
