<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthManager extends Controller
{
    public function login()  {
        return view('auth.login');
    }
    public function loginPost(request $request){
          $request->validate([
            'email'=> 'required',
            'password'=> 'required|min:8',
          ]);
          $credentials = $request->only('email','password');

          if(Auth::attempt($credentials)){
            return redirect()->intended(route("home"));
          }
          return redirect('login')->with("error","Invalid Email or password");
    }


     public function register(){
      return view('auth.register');
     }

     public function registerPost(Request $request){
       $request->validate([
        'fullname'=>'required',
        'email'=>'required|email',
        'password'=>'required'
       ]);
       $user = new User();
       $user->name = $request->fullname;
       $user->email= $request->email;
       $user->password =$request->password;
       if($user->save()){
        return redirect(route("login"))->with('success','Registration Successfull');
       }
       return redirect(route('register'))->with('error','registration failed');
     }
}
