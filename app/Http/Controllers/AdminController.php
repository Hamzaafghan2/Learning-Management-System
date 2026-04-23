<?php

namespace App\Http\Controllers;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Course;
use App\Models\User;
use App\Providers\RouteServicesProviders;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\view\view;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

use Spatie\Permission\Models\Permission;
use function Laravel\Prompts\password;
use function Pest\Laravel\get;

class AdminController extends Controller
{
    public function AdminDashboard(){
        return view('admin.index');
    }


     public function adminLogout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        
     $notification = array(
        'message'=>"Logout Successfully",
        'alert-type'=>'info'
    );

        return redirect('/admin/login')->with($notification);
    }

    public function adminLogin(){
        return view('admin.admin_login');
    }

    public function AdminProfile(){
      $id=auth::user()->id;
      $profileData=User::find($id);
      return view('admin.admin_profile_view',compact('profileData'));
    }

    public function AdminProfileStore(request $request): RedirectResponse{
     $id=Auth::user()->id;
     $data=User::find($id);
     $data->name=$request->name;
    $data->username=$request->username;
    $data->email=$request->email;
    $data->phone=$request->phone;
    $data->address=$request->address;
    
    if($request->file('photo')){
        $file=$request->file('photo');
        @unlink(public_path('upload/admin_images/'.$data->photo));
        $filename=date('YmdHi').$file->getClientOriginalName();
        $file->move(public_path('upload/admin_images'),$filename);
        $data['photo'] = $filename;
    }
    $data->save();
    $notification = array(
        'message'=>"Admin Profile Updated Successfully",
        'alert-type'=>'success'
    );
    return redirect()->back()->with($notification);
}//End method

public function AdminChangePassword(){
    $id=auth::user()->id;
      $profileData=User::find($id);
      return view('admin.admin_change_password',compact('profileData'));
}
public function AdminPasswordUpdate(request $request){
 $request->validate([
    'old_password'=>'required',
    'new_password'=>'required|confirmed'
 ]);
 if(!Hash::check($request->old_password,Auth::user()->password)){
    //   $data->save();
    $notification = array(
        'message'=>"Old Password doesn't Match!",
        'alert-type'=>'error'
    );
    return back()->with($notification);
}
//update the password
User::whereId(auth::user()->id)->update([
    'password'=>Hash::make($request->new_password)
]);
  
    $notification = array(
        'message'=>"password Changes Successfully",
        'alert-type'=>'Success'
    );
    return redirect()->back()->with($notification);
}//End method


public function BecomeIstructor(){
        return view('frontend.instructor.reg_instructor');
}//End method


public function AllInstructor(){
    $allinstructor=User::where('role','instructor')->latest()->get();
    return view('admin.backend.instructor.all_instructor',compact('allinstructor'));

}//End method
 
public function UpdateUserStatus(Request $request)
{
    $userId = $request->input('user_id');
    $isChecked = $request->input('is_checked', 0); // ✅ fixed spelling

    $user = User::find($userId);

    if ($user) {
        $user->status = $isChecked;
        $user->save();

        return response()->json(['message' => 'User Status Updated Successfully']);
    } else {
        return response()->json(['message' => 'User not found'], 404);
    }
}//End method 

   public function AdminAllCourse()  {
     $course=Course::latest()->get();
     return view('admin.backend.courses.all_course',compact('course'));
   }//End method 

   public function UpdateCourseStatus(Request $request)
{
    $courseId = $request->input('course_id');
    $isChecked = $request->input('is_checked', 0); // ✅ fixed spelling

    $course = Course::find($courseId);

    if ($course) {
        $course->status = $isChecked;
        $course->save();

        return response()->json(['message' => 'Course Status Updated Successfully']);
    } else {
        return response()->json(['message' => 'Course not found'], 404);
    }
 }//End method

 public function AdminCourseDetails($id){
  $course = Course::find($id);
  return view('admin.backend.courses.course_details',compact('course'));
 }//End method 


 //================//
 ///Admin User All Method ///
 //===================//
//  public function AllAdmin(){
//   $alladmin = User::where('role','admin')->get();
//   return view('admin.backend.pages.admin.all_admin',compact('alladmin'));
//  }//End method 

//  public function AddAdmin(){

//         $roles = Role::all();
//         return view('admin.backend.pages.admin.add_admin',compact('roles'));

//     }// End Method

//     // public function StoreAdmin(Request $request){

//     //     $user = new User();
//     //     $user->username = $request->username;
//     //     $user->name = $request->name;
//     //     $user->email = $request->email;
//     //     $user->phone = $request->phone;
//     //     $user->address = $request->address;
//     //     $user->password = Hash::make($request->password);
//     //     $user->role = 'admin';
//     //     $user->status = '1';
//     //     $user->save();

//     //     if ($request->roles) {
//     //      $role = Role::find($request->roles);  // find role by ID
//     //      $user->assignRole($role->name);       // assign role by NAME
//     //    }

//     //     $notification = array(
//     //         'message' => 'New Admin Inserted Successfully',
//     //         'alert-type' => 'success'
//     //     );
//     //     return redirect()->route('all.admin')->with($notification); 

//     // }// End Method


// public function StoreAdmin(Request $request){

//     $user = new User();
//     $user->username = $request->username;
//     $user->name = $request->name;
//     $user->email = $request->email;
//     $user->phone = $request->phone;
//     $user->address = $request->address;
//     $user->password = Hash::make($request->password);
//     $user->role = 'admin';
//     $user->status = '1';
//     $user->save();

//     // FIX: Get role name from ID
//     if ($request->roles) {
//         $role = Role::find($request->roles);  // find role by ID
//         $user->assignRole($role->name);       // assign role by NAME
//     }

//     $notification = array(
//         'message' => 'New Admin Inserted Successfully',
//         'alert-type' => 'success'
//     );
//     return redirect()->route('all.admin')->with($notification); 
// }

// public function EditAdmin($id){

//         $user = User::find($id);
//         $roles = Role::all();
//         return view('admin.backend.pages.admin.edit_admin',compact('user','roles'));

//     }// End Method

//       public function UpdateAdmin(Request $request,$id){

//         $user = User::find($id);
//         $user->username = $request->username;
//         $user->name = $request->name;
//         $user->email = $request->email;
//         $user->phone = $request->phone;
//         $user->address = $request->address; 
//         $user->role = 'admin';
//         $user->status = '1';
//         $user->save();

//         $user->roles()->detach();
//         if ($request->roles) {
//          $role = Role::find($request->roles);  // find role by ID
//          $user->assignRole($role->name);       // assign role by NAME
//        }

//         $notification = array(
//             'message' => 'Admin Updated Successfully',
//             'alert-type' => 'success'
//         );
//         return redirect()->route('all.admin')->with($notification); 

//     }// End Method


 /// Admin User All Method ////////////

   /// Admin User All Method ////////////

    public function AllAdmin(){

        $alladmin = User::where('role','admin')->get();
        return view('admin.backend.pages.admin.all_admin',compact('alladmin'));

    }// End Method

    public function AddAdmin(){

        $roles = Role::all();
        return view('admin.backend.pages.admin.add_admin',compact('roles'));

    }// End Method

    public function StoreAdmin(Request $request){

        $user = new User();
        $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->password = Hash::make($request->password);
        $user->role = 'admin';
        $user->status = '1';
        $user->save();

        if ($request->roles) {
            $user->assignRole($request->roles);
        }
        

        $notification = array(
            'message' => 'New Admin Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.admin')->with($notification); 

    }// End Method


    public function EditAdmin($id){

        $user = User::find($id);
        $roles = Role::all();
        return view('admin.backend.pages.admin.edit_admin',compact('user','roles'));

    }// End Method

    public function UpdateAdmin(Request $request,$id){

        $user = User::find($id);
        $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address; 
        $user->role = 'admin';
        $user->status = '1';
        $user->save();

        $user->roles()->detach();
        if ($request->roles) {
            $user->assignRole($request->roles);
        }

        $notification = array(
            'message' => 'Admin Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.admin')->with($notification); 

    }// End Method

    public function DeleteAdmin($id)  {
        $user = User::find($id);
            if(!is_null($user)){
                $user->delete();
            }
      $notification = array(
            'message' => 'Admin Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification); 

    }// End Method


 }

//56 folder 2 video 
