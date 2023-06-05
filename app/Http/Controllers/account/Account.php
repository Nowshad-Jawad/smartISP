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

    public function customerPackage($customer){

        return response()->json(['customer' => $customer]);
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
