<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
class UserController extends Controller
{
    //

    public function index() {
        return view('frontend.index');
    }//end function

    public function UserProfile()  {
        
         $id=auth::user()->id;
      $profileData=User::find($id);
      return view('frontend.dashboard.edit_profile',compact('profileData'));
    }//end function


     public function UserProfileUpdate(Request $request){

        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->username = $request->username;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
           $file = $request->file('photo');
           @unlink(public_path('upload/user_images/'.$data->photo));
           $filename = date('YmdHi').$file->getClientOriginalName();
           $file->move(public_path('upload/user_images'),$filename);
           $data['photo'] = $filename; 
        }

        $data->save();

        $notification = array(
            'message' => 'User Profile Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }// End Method 
  
public function UserLogout(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
        'message'=>"Logout Successfully",
        'alert-type'=>'info'
    );

        return redirect('/login')->with($notification);
    
        
  }//end function 

  public function UserChangePassword()  {

    return view('frontend.dashboard.change_password');
     
    
  }
    
    public function UserPasswordUpdate(request $request){
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
}//end method 

 public function LiveChat()  {
        
        
      return view('frontend.dashboard.live_chat');
    }//end function

}



// public function UserRegister(Request $request)
// {
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'email' => 'required|email|unique:users',
//         'password' => 'required|min:6|confirmed',
//     ]);

//     $user = User::create([
//         'name' => $request->name,
//         'email' => $request->email,
//         'password' => Hash::make($request->password),
//         'role' => 'user', // or 'admin' / 'instructor' if you want fixed role
//     ]);

//     Auth::login($user);

//     return redirect('/dashboard');
// }//end function
