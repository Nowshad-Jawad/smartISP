<?php

namespace App\Http\Controllers\sms;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SmsApi;
use App\Models\SmsTemplates;
use App\Models\Customer;
use App\Models\Manager;
use App\Models\SmsGroup;
use App\Models\SmsGroupUsers;

class SMS extends Controller
{
    public function viewSMSTemplate(){
        $sms_templates = SmsTemplates::all();
        $sms_apis = SmsApi::all();
        return view('content.sms.sms-template', compact('sms_templates', 'sms_apis'));
    }

    public function viewSMSApi(){
        $sms_api = SmsApi::all();
        return view('content.sms.sms-api', compact('sms_api'));
    }

    public function storeSMSApi(Request $request){
        $request->validate([
            'name' => 'required',
            'api' => 'required',
            'api_key' => 'required',
            'secret_key' => 'required'
        ]);

        SmsApi::create([
            'name' => $request->name,
            'api_url' => $request->api,
            'api_key' => $request->api_key,
            'sender_id' => $request->secret_key,
            'client_id' => $request->caller_id
        ]);

        return redirect()->back();
    }

    public function storeSmsTemplate(Request $request){
        $request->validate([
            'name' => 'required',
            'api' => 'required',
            'template_for' => 'required',
            'template' => 'required'
        ]);

        SmsTemplates::create([
            'name' => $request->name,
            'sms_apis_id' => $request->api,
            'type' => $request->template_for,
            'template' => $request->template
        ]);

        return redirect()->back();
    }

    public function createSendSms(){
        $customers = Customer::all();
        $managers = Manager::all();
        $sms_groups = SmsGroup::all();
        return view('content.sms.send-sms', compact('customers', 'managers', 'sms_groups'));
    }

    public function storeGroup(Request $request){
        $request->validate([
            'name' => 'required',
            'user_type' => 'required',
            'users' => 'required'
        ]);

        $group = SmsGroup::create([
            'name' => $request->name,
            'group_type' => $request->user_type,
        ]);

        foreach($request->users as $value){
           $typeandid = explode(" ", $value);
           if($typeandid[0] == 'customer'){
            $customer = Customer::where('id', $typeandid[1])->first();
            SmsGroupUsers::create([
                'smsgroup_id' => $group->id,
                'customer_id' => $typeandid[1]
            ]);
           }

           if($typeandid[0] == 'manager'){
            $manager = Manager::where('id', $typeandid[1])->first();
            SmsGroupUsers::create([
                'smsgroup_id' => $group->id,
                'manager_id' => $typeandid[1]
            ]);
           }
        }

        return redirect()->back();
    }

    public function getGroupUsers(Request $request){

        $element = $request->element_id;
        $element = explode('_', $element);
        $sms_users = SmsGroupUsers::where('smsgroup_id', $element[1])->get();
        $users_array = [];
        foreach($sms_users as $sms_user){
            array_push($users_array, $sms_user->user);
        }
        return response()->json(['element' => $users_array]);
    }

    public function sendSms(Request $request){

        dd($request->user_2);
    }
}
