<?php

namespace App\Http\Controllers;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServicesProviders;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\view\view;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class InstructorController extends Controller
{
    public function instructorbord(){
        return view('instructor.index');
    }

    public function instructorLogout(request $request){
       
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

         $notification = array(
        'message'=>"Logout Successfully",
        'alert-type'=>'info'
    );

        return redirect('/instructor/login')->with($notification);
    
    }

    public function instructorLogin()  {
        return view ('instructor.instructor_login');
    }// End function

     public function instructorProfile(){
      $id=auth::user()->id;
      $profileData=User::find($id);
      return view('instructor.instructor_profile_view',compact('profileData'));
    }//end of the method 

    public function instructorProfileStore(request $request): RedirectResponse{
     $id=Auth::user()->id;
     $data=User::find($id);
     $data->name=$request->name;
    $data->username=$request->username;
    $data->email=$request->email;
    $data->phone=$request->phone;
    $data->address=$request->address;
    
    if($request->file('photo')){
        $file=$request->file('photo');
        @unlink(public_path('upload/instructor_images/'.$data->photo));
        $filename=date('YmdHi').$file->getClientOriginalName();
        $file->move(public_path('upload/instructor_images'),$filename);
        $data['photo'] = $filename;
    }
    $data->save();
    $notification = array(
        'message'=>"instructor Profile Updated Successfully",
        'alert-type'=>'success'
    );
    return redirect()->back()->with($notification);
}//end of the metod

public function instructorChangePassword(){
    $id=auth::user()->id;
      $profileData=User::find($id);
      return view('instructor.instructor_change_password',compact('profileData'));
}//end fo the method

public function instructorPasswordUpdate(request $request){
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
    return back()->with($notification);
}


}
