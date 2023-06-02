<?php

namespace App\Http\Controllers\network;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Zone as ZoneModel;
use App\Models\SubZone;
use App\Models\OLT;
use App\Models\ONU;
use App\Models\User;

class Network extends Controller
{
    public function addZone(){
        return view('content.network.add-zone');
    }

    public function storeZone(Request $request){
        $request->validate([
            'name' => 'required',
            'abbr' => 'required',
            'status' => 'required'
        ]);

        if($request->status == 'on'){
            $status = true;
        }
        else{
            $status = false;
        }

        ZoneModel::create([
            'name' => $request->name,
            'abbreviation' => $request->abbr,
            'status' => $status
        ]);

        return redirect()->back();
    }

    public function viewZone(){
        $zones = ZoneModel::all();
        return view('content.network.view-zone', compact('zones'));
    }

    public function editZone($id){
        $zone = ZoneModel::where('id', $id)->first();
        return view('content.network.edit-zone', compact('zone'));
    }
    
    public function updateZone(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'abbr' => 'required',
            'status' => 'required'
        ]);

        if($request->status == 'on'){
            $status = true;
        }
        else{
            $status = false;
        }

        $zone = ZoneModel::find($id);
        $zone->name = $request->name;
        $zone->abbreviation = $request->abbr;
        $zone->status = $status;
        $zone->save();

        return redirect()->back();
    }

    public function addSubZone(){
        $zones = ZoneModel::where('status', true)->get();
        return view('content.network.add-sub-zone', compact('zones'));
    }

    public function storeSubZone(Request $request){
        $request->validate([
            'name' => 'required',
            'zone_id' => 'required',
        ]);

        SubZone::create([
            'name' => $request->name,
            'zone_id' => $request->zone_id,
        ]);

        return redirect()->back();
    }

    public function viewSubZone(){
        $sub_zones = SubZone::all();
        return view('content.network.view-sub-zone', compact('sub_zones'));
    }

    public function editSubZone($id){
        $sub_zone = SubZone::where('id', $id)->first();
        $zones = ZoneModel::all();
        return view('content.network.edit-sub-zone', compact('sub_zone', 'zones'));
    }

    public function updateSubZone(Request $request, $id){
        $request->validate([
            'name' => 'required',
            'zone_id' => 'required',
        ]);

        $sub_zone = SubZone::find($id);
        $sub_zone->name = $request->name;
        $sub_zone->zone_id = $request->zone_id;
        $sub_zone->save();

        return redirect()->back();
    }

    public function addOLT(){
        $zones = ZoneModel::all();
        $sub_zones = SubZone::all();
        return view('content.network.add-olt', compact('zones', 'sub_zones'));
    }

    public function storeOLT(Request $request){
        $request->validate([
            'name' => 'required',
            'zone_id' => 'required',
            'sub_zone_id' => 'required',
            'type' => 'required',
            'pon' => 'required',
            'management_ip' => 'required',
            'total_onu' => 'required',
            'vlan_id' => 'required',
            'vlan_ip' => 'required',
        ]);

        OLT::create([
            'name' => $request->name,
            'zone_id' => $request->zone_id,
            'sub_zone_id' => $request->sub_zone_id,
            'type' => $request->type,
            'non_of_pon_port' => $request->pon,
            'management_ip' => $request->management_ip,
            'total_onu' => $request->total_onu,
            'management_vlan_id' => $request->vlan_id,
            'management_vlan_ip' => $request->vlan_ip
        ]);

        return redirect()->back();
    }

    public function addONU(){

        $olts = OLT::all();
        $users = User::all();
        return view('content.network.add-onu', compact('olts', 'users'));
    }

    public function oltDetailsForAddOnu($id){

        $olt = OLT::find($id);
        $zone = $olt->zone->name;
        return response()->json(['olt' => $olt, 'zone' => $zone]);
    }

    public function storeOnu(Request $request){

        $request->validate([
            'name' => 'required',
            'mac' => 'required',
            'olt_id' => 'required',
            'pon_port' => 'required',
            'onu_id' => 'required',
            'rx_power' => 'required',
            'distance' => 'required',
            // 'user_id' => 'required',
            'zone_id' => 'required',
        ]);

        if($request->vlan_tagged != null){
            $request->validate([
                'vlan_id' => 'required'
            ]);

            $vlan_tagged = true;
        }
        else{
            $vlan_tagged = false;
        }

        $zone = ZoneModel::where('name', $request->zone_id)->first();

        ONU::create([
            'name' => $request->name,
            'mac' => $request->mac,
            'olt_id' => $request->olt_id,
            'pon_port' => $request->pon_port,
            'onu_id' => $request->onu_id,
            'rx_power' => $request->rx_power,
            'distance' => $request->distance,
            'user_id' => $request->user_id,
            'zone_id' => $zone->id,
            'vlan_tagged' => $vlan_tagged,
            'vlan_id' => $request->vlan_id,
            'status' => 1
        ]);

        return redirect()->back();


    }
}
