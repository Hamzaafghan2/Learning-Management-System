<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WishListController extends Controller
{
 public function AddToWishList(Request $request,$course_id){
  if(Auth::check()){
    $exists= Wishlist::where('user_id',Auth::id())->where('course_id',$course_id)->first();

    if(!$exists){
        Wishlist::insert([
            'user_id'=>Auth::id(),
            'course_id'=>$course_id,
            'created_at'=>Carbon::now(),
        ]);
    return response()->json(['success' => 'Successfuly added on your wishlist']);
    
    }else {
         return response()->json(['error' => 'This product is already on your wishlist']);
    }
  }else{
     return response()->json(['error' => 'At First login Your Account']);
  }
 }//End method
 
 public function AllWishist(){

    return view('frontend.wishlist.all_wishlist');
 }//End method 

 public function GetWishlistCourse(){
    $wishlist = Wishlist::with('course')->where('user_id',Auth::id())->latest()->get();
    $wishQty=Wishlist::count();
    return response()->json(['wishlist'=>$wishlist, 'wishQty'=>$wishQty]);
 }//End method 

 public function RemoveWishlist($id)  {
   Wishlist::where('user_id',Auth::id())->where('id',$id)->delete();
   return response()->json(['success'=>"Successfully Remove the Course"]);
 }//End method

}
