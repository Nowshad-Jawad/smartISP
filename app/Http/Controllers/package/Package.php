<?php

namespace App\Http\Controllers\package;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mikrotik;
use App\Models\IPpool;
use App\Models\Package as PackageModel;

class Package extends Controller
{
    public function addPackage(){
        $mikrotiks = Mikrotik::all();
        $ips = IPpool::all();
        return view('content.package.add-package', compact('mikrotiks', 'ips'));
    }

    public function storePackage(Request $request){
        $request->validate([
            'mikrotik_id' => 'required',
            'name' => 'required',
            'synonym' => 'required',
            'ip_pool' => 'required',
            'price' => 'required',
            'manager_price' => 'required',
            'duration' => 'required',
            'period' => 'required',
            'status' => 'required'
        ]);

        if($request->status == 'on'){
            $status = true;
        }
        else{
            $status = false;
        }

        $package = PackageModel::create([
            'name' => $request->name,
            'type' => 'PPPOE',
            'synonym' => $request->synonym,
            'mikrotik_id' => $request->mikrotik_id,
            'pool_id' => $request->ip_pool,
            'price' => $request->price,
            'pop_price' => $request->manager_price,
            'status' => $status
        ]);

        $expireafter = 0;
        switch ($request->duration) {
            case 'Minutes':
                $expireafter = $request->period * 60;
                break;
            case 'Hours':
                $expireafter = $request->period * 60 * 60;
                break;
            case 'Days':
                $expireafter = $request->period * 60 * 60 * 24;
                break;
            case 'Weeks':
                $expireafter = $request->period * 60 * 60 * 24 * 7;
                break;
            case 'Months':
                $expireafter = $request->period * 60 * 60 * 24 * 30;
                break;
            default:
                $expireafter = 0;
        }

        $package->validdays = $expireafter;
        $package->durationmeasure = $request->period.' '.$request->duration;

        if($request->fixed_expiry != null){
            $package->fixed_expire_time_status = true;
            $package->fixed_expire_time = $request->fixed_expiry_day;
        }
        else{
            $package->fixed_expire_time_status = false;
            $package->fixed_expire_time = null;
        }

        $package->save();
        return redirect()->back();
    } 

    public function viewPackage(){
        $packages = PackageModel::all();
        return view('content.package.view-package', compact('packages'));
    }

    public function editPackage($id){
        $package = PackageModel::where('id', $id)->first();
        if($package->durationmeasure != null){
            $duration_measure = explode(' ', $package->durationmeasure);
        }
        else{
            $duration_measure = null; 
        }
        $mikrotiks = Mikrotik::all();
        $ips = IPpool::all();
        return view('content.package.edit-package', compact('package', 'duration_measure', 'mikrotiks', 'ips'));
    }

    public function updatePackage(Request $request, $id){
        $request->validate([
            'mikrotik_id' => 'required',
            'name' => 'required',
            'synonym' => 'required',
            'ip_pool' => 'required',
            'price' => 'required',
            'manager_price' => 'required',
            'duration' => 'required',
            'period' => 'required',
            'status' => 'required'
        ]);

        if($request->status == 'on'){
            $status = true;
        }
        else{
            $status = false;
        }

        if($request->fixed_expiry == 'on'){
            $request->validate([
                'fixed_expiry_day' => 'required'
            ]);
        }

        $package = PackageModel::find($id);
        $package->name = $request->name;
        $package->synonym = $request->synonym;
        $package->mikrotik_id = $request->mikrotik_id;
        $package->pool_id = $request->ip_pool;
        $package->price = $request->price;
        $package->pop_price = $request->manager_price;
        $package->status = $status;

        $expireafter = 0;
        switch ($request->duration) {
            case 'Minutes':
                $expireafter = $request->period * 60;
                break;
            case 'Hours':
                $expireafter = $request->period * 60 * 60;
                break;
            case 'Days':
                $expireafter = $request->period * 60 * 60 * 24;
                break;
            case 'Weeks':
                $expireafter = $request->period * 60 * 60 * 24 * 7;
                break;
            case 'Months':
                $expireafter = $request->period * 60 * 60 * 24 * 30;
                break;
            default:
                $expireafter = 0;
        }

        $package->validdays = $expireafter;
        $package->durationmeasure = $request->period.' '.$request->duration;

        if($request->fixed_expiry != null){
            $package->fixed_expire_time_status = true;
            $package->fixed_expire_time = $request->fixed_expiry_day;
        }
        else{
            $package->fixed_expire_time_status = false;
            $package->fixed_expire_time = null;
        }
        
        $package->save();
        return redirect()->back();


    }
}
