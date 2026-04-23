<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function AllPermission(){
    $permissions = Permission::all();
    return view ('admin.backend.pages.permission.all_permission',compact('permissions'));
    }//End method

    public function AddPermission(){
        return view('admin.backend.pages.permission.add_permission');
    }//End mtethod 

    public function StorePermission(Request $request){
        Permission::create([
            'name'=> $request->name,
            'group_name'=> $request->group_name,
        ]);
       $ntification = array(
            'message' => 'permission Created Successfully!',
            'alert-type' => 'success'
        );
        return redirect()->route('all.permission')->with($ntification);
    }// End method 

    public function EditPermission($id){

        $permissions = Permission::find($id);
        return view('admin.backend.pages.permission.edit_permission',compact('permissions'));
    }//End method 
    public function UpdatePermission(Request $request){

        $per_id = $request->id;

        Permission::find($per_id)->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        $notification = array(
            'message' => 'Permission Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.permission')->with($notification);  

    }// End Method 

    public function DeletePermission($id){
       Permission::find($id)->delete();

        $ntification = array(
            'message' => 'permission Deleted Successfully!',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($ntification);
    }//End method 
    public function ImportPermission(){
        return view ('admin.backend.pages.permission.import_permission');
    }//End method 

    //========================//
    //===All Roles Route ====//

    public function AllRoles(){
        $roles = Role::all();
        return view('admin.backend.pages.roles.all_roles',compact('roles'));
    }//End method 

    public function AddRoles(){
   return view('admin.backend.pages.roles.add_roles');

    }// End method 
    public function StoreRoles(Request $request){
       Role::create([
        'name'=>$request->name,
       ]);
        $notification = array(
            'message' => 'Rolse Created Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles')->with($notification); 
    }//End method 
    public function EditRoles($id){
        $roles = Role::find($id);
        return  view('admin.backend.pages.roles.edit_roles',compact('roles'));
    }//End method 

    public function UpdateRoles(Request $request){
        $role_id = $request->id;
        Role::find($role_id)->update([
            'name'=>$request->name,
        ]);
         $notification = array(
            'message' => 'Rolse Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles')->with($notification);
    }// End method 

    public function DeleteRoles($id){
        Role::find($id)->delete();

        $notification = array(
            'message' => 'Rolse Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }//end method 

    public function AddRolesPermission(){
        $roles = Role::all();
        $permissions = Permission::all();
        $permission_group = User::getpermissionGroups();
        return view('admin.backend.pages.rolesetup.add_roles_permission',compact('roles','permission_group','permissions'));
    }//end method 

    public function RolePermissionStore(Request $request){
        $data = array();
        $permissions = $request->permission;
        foreach($permissions as $key=>$item){
            $data['role_id'] = $request->role_id;
            $data['permission_id'] = $item;
            DB::table('role_has_permissions')->insert($data);
        }
        $notification = array(
            'message' => 'Rolse Permission Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles.permission')->with($notification); 
    }// End method  

     public function AllRolesPermission(){

        $roles = Role::all();
        return view('admin.backend.pages.rolesetup.all_roles_permission',compact('roles'));

    }// End Method 
       public function AdminEditRoles($id){

        $role = Role::find($id);
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();

        return view('admin.backend.pages.rolesetup.edit_roles_permission',compact('role','permission_groups','permissions'));


    }// End Method 
   public function AdminUpdateRoles(Request $request, $id){

        $role = Role::find($id);
        $permissions = $request->permission;
        
        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        $notification = array(
            'message' => 'Role Permission Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles.permission')->with($notification); 

    }// End Method 

    public function AdminDeleteRoles($id){
        $role = Role::find($id);
        if(!is_null($role)){
            $role->delete();
        }

         $notification = array(
            'message' => 'Role Permission Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification); 
    }//end method 
}
    