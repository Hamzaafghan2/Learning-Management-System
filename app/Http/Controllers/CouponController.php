<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CouponController extends Controller
{
    public function AdminAllCoupon(){
        $coupon = Coupon::latest()->get();
        return view('admin.backend.coupon.coupon_all',compact('coupon'));
    }//End method 

    public function AdminAddCoupon()  {
        return view('admin.backend.coupon.coupon_add'); 
    }//End method 

    public function AdminStoreCoupon(Request $request)  {
         Coupon::insert([
           'coupon_name'=>strtoupper($request->coupon_name),
           'coupon_discount'=> $request->coupon_discount,
           'coupon_validity'=> $request->coupon_validity,
           'created_at'=>Carbon::now(),
        ]);
        $notification = array(
        'message'=>"Coupon  Inserted Successfully",
        'alert-type'=>'success'
    );
    return redirect()->route('admin.all.coupon')->with($notification);
    }//End method 

    public function AdminEditCoupon($id)  {
        $coupon= Coupon::find($id);
        return view('admin.backend.coupon.coupon_edit',compact('coupon'));
    }/// End method 

    public function AdminUpdateCoupon(Request $request){
         $coupon_id =$request->id;

Coupon::find($coupon_id)->update([

           'coupon_name'=>strtoupper($request->coupon_name),
           'coupon_discount'=> $request->coupon_discount,
           'coupon_validity'=> $request->coupon_validity,
           'created_at'=>Carbon::now(),
        ]);
        $notification = array(
        'message'=>"Coupon Updated Successfully",
        'alert-type'=>'success'
    );
    return redirect()->route('admin.all.coupon')->with($notification);
    }/// End method

    public function AdminDeleteCoupon($id)  {
        Coupon::find($id)->delete();

         $notification = array(
        'message'=>"Coupon Updated Successfully",
        'alert-type'=>'success'
    );
    return redirect()->back()->with($notification);
    }//End method

    //==============================////
/////////INSTRUCTOR ALL COUPON METHOD /////
////==============================////
    public function InstructorAllCoupon(){
 
   $id = Auth()->user()->id;
     $coupon = Coupon::where('instructor_id',$id)->latest()->get();
     return view('instructor.coupon.coupon_all',compact('coupon'));

    }//End method

    public function InstructorAddCoupon(){
        $id = Auth::user()->id;
    $courses = Course::where('instructor_id',$id)->get();
        return view('instructor.coupon.coupon_add',compact('courses'));
    }//End method

    public function InstructorStoreCoupon(Request $request)  {

        $id = Auth::user()->id;
        Coupon::insert([
            'instructor_id'=> $id,
            'coupon_name'=>strtoupper($request->coupon_name),
            'coupon_discount'=> $request->coupon_discount,
            'coupon_validity'=> $request->coupon_validity,
            'course_id'=> $request->course_id,
            'created_at'=>Carbon::now(),
         ]);
        $notification = array(
        'message'=>"Coupon  Inserted Successfully",
        'alert-type'=>'success'
    );
    return redirect()->route('instructor.all.coupon')->with($notification);

    }//End method

    public function InstructorEditCoupon($id)  {
        $coupon= Coupon::find($id);
          $insid = Auth::user()->id;
         $courses = Course::where('instructor_id',$insid)->get();
        return view('instructor.coupon.coupon_edit',compact('coupon','courses'));
    }/// End method

    public function InstructorUpdateCoupon(Request $request){
         $coupon_id =$request->coupon_id;

         Coupon::find($coupon_id)->update([

            'instructor_id'=> Auth::user()->id,
            'coupon_name'=>strtoupper($request->coupon_name),
            'coupon_discount'=> $request->coupon_discount,
            'coupon_validity'=> $request->coupon_validity,
            'course_id'=> $request->course_id,
            'created_at'=>Carbon::now(),
        ]);
        $notification = array(
        'message'=>"Coupon Updated Successfully",
        'alert-type'=>'success'
    );
    return redirect()->route('instructor.all.coupon')->with($notification);
    }/// End method 

    public function InstructorDeleteCoupon($id)  {
        Coupon::find($id)->delete();

         $notification = array(
        'message'=>"Coupon Updated Successfully",
        'alert-type'=>'success'
    );
    return redirect()->back()->with($notification);
    }//End method

}
