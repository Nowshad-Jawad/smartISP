<?php

namespace App\Http\Controllers\manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Manager as ManagerModel;
use App\Models\Zone;
use App\Models\SubZone;
use App\Models\Mikrotik;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RoleHasPermission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class Manager extends Controller
{
    public function listManagers(){
        $managers = ManagerModel::all();
        $zones = Zone::all();
        $sub_zones = SubZone::all();
        $mikrotiks = Mikrotik::all();
        $roles = Role::all();
        return view('content.manager.manager-list', compact('managers', 'zones', 'sub_zones', 'mikrotiks', 'roles'));
    }

    public function storeManager(Request $request){
        $request->validate([
            'type' => 'required',
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.ManagerModel::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'required',
            'zone_id' => 'required',
            'subzone_id' => 'required',
            'mikrotik_id' => 'required',
            'address' => 'required',
        ]);

        $manager = ManagerModel::create([
            'type' => $request->type,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'zone_id' => $request->zone_id,
            'sub_zone_id' => $request->subzone_id,
            'mikrotik_id' => $request->mikrotik_id,
            'address' => $request->address,
            'grace_allowed' => $request->grace
        ]);

        if($request->prefix == 'on'){
            $prefix = true;
            $request->validate([
                'prefix_text' => 'required'
            ]);

            $manager->prefix = $prefix;
            $manager->prefix_text = $request->prefix_text;
            $manager->save();
        }
        else{
            $prefix = false;
            $manager->prefix = $prefix;
            $manager->save();
        }

        return redirect()->back();
    }

    public function updateManager(Request $request, $id){

        $request->validate([
            'type' => 'required',
            'name' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'required',
            'zone_id' => 'required',
            'subzone_id' => 'required',
            'mikrotik_id' => 'required',
            'address' => 'required',
        ]);

        $manager = ManagerModel::find($id);
        if($request->email == $manager->email){
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255'],
            ]);
        }
        else{
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:'.ManagerModel::class],
            ]);
        }
        $manager->type = $request->type;
        $manager->name = $request->name;
        $manager->email = $request->email;
        $manager->password = $request->password;
        $manager->phone = $request->phone;
        $manager->zone_id = $request->zone_id;
        $manager->sub_zone_id = $request->subzone_id;
        $manager->mikrotik_id = $request->mikrotik_id;
        $manager->address = $request->address;
        $manager->grace_allowed = $request->grace;

        if($request->prefix == 'on'){
            $prefix = true;
            $request->validate([
                'prefix_text' => 'required'
            ]);

            $manager->prefix = $prefix;
            $manager->prefix_text = $request->prefix_text;
            $manager->save();
        }
        else{
            $prefix = false;
            $manager->prefix = $prefix;
            $manager->save();
        }

        return redirect()->back();

        
    }

    public function listRoles(){
        $roles = Role::all();
        return view('content.role.role-list', compact('roles'));
    }

    public function storeRole(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:'.Role::class]
        ]);

        Role::create([
            'name' => $request->name
        ]);

        return redirect()->back();
    }

    public function updateRole(Request $request, $id){
        $role = Role::find($id);

        if($request->name != ""){
            if($request->name == $role->name){
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                ]);
            }
            else{
                $request->validate([
                    'name' => ['required', 'string', 'max:255', 'unique:'.Role::class],
                ]);
            }
    
            $role->name = $request->name;
            $role->update();
    
            return redirect()->back();
        }
        else{
            echo "something went wrong";
        }
    }

    public function assignPermission(Request $request, $id){
        
        $role = Role::find($id);
        if($request->manager_list == 'on'){
            $permission = Permission::where('name', 'Manager List')->first();
            $role_has_permission = RoleHasPermission::where('role_id', $role->id)->where('permission_id', $permission->id)->first();
            if($role_has_permission == null){
                RoleHasPermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }   
        }
        else{
            $permission = Permission::where('name', 'Manager List')->first();
            $role_has_permission = RoleHasPermission::where('role_id', $role->id)->where('permission_id', $permission->id)->first();
            if($role_has_permission != null){
                $role_has_permission->delete();
            }  
        }

        if($request->add_manager == 'on'){
            $permission = Permission::where('name', 'Manager Add')->first();
            $role_has_permission = RoleHasPermission::where('role_id', $role->id)->where('permission_id', $permission->id)->first();
            if($role_has_permission == null){
                RoleHasPermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }   
        }
        else{
            $permission = Permission::where('name', 'Manager Add')->first();
            $role_has_permission = RoleHasPermission::where('role_id', $role->id)->where('permission_id', $permission->id)->first();
            if($role_has_permission != null){
                $role_has_permission->delete();
            }  
        }

        
        if($request->edit_manager == 'on'){
            $permission = Permission::where('name', 'Manager Edit')->first();
            $role_has_permission = RoleHasPermission::where('role_id', $role->id)->where('permission_id', $permission->id)->first();
            if($role_has_permission == null){
                RoleHasPermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }   
        }
        else{
            $permission = Permission::where('name', 'Manager Edit')->first();
            $role_has_permission = RoleHasPermission::where('role_id', $role->id)->where('permission_id', $permission->id)->first();
            if($role_has_permission != null){
                $role_has_permission->delete();
            }  
        }

        if($request->delete_manager == 'on'){
            $permission = Permission::where('name', 'Manager Delete')->first();
            $role_has_permission = RoleHasPermission::where('role_id', $role->id)->where('permission_id', $permission->id)->first();
            if($role_has_permission == null){
                RoleHasPermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }   
        }
        else{
            $permission = Permission::where('name', 'Manager Delete')->first();
            $role_has_permission = RoleHasPermission::where('role_id', $role->id)->where('permission_id', $permission->id)->first();
            if($role_has_permission != null){
                $role_has_permission->delete();
            }  
        }


        return redirect()->back();
    }

    public function addRoleToManager(Request $request, $id){

        $request->validate([
            'role_id' => 'required'
        ]);

        $manager = ManagerModel::find($id);
        $manager->role_id = $request->role_id;
        $manager->save();

        return redirect()->back();

    }

}
