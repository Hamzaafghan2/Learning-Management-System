<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
 use App\Models\Order;
use App\Models\Payment;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
 use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    
    public function AdminPendingOrder(){
        $payment = Payment::where('status', 'pending')->orderBy('id','DESC')->get();
        return view('admin.backend.orders.pending_orders',compact('payment'));
    }//End method

     public function AdminOrderDetails($payment_id){

        $payment = Payment::where('id',$payment_id)->first();
        $orderItem = Order::where('payment_id',$payment_id)->orderBy('id','DESC')->get();

        return view('admin.backend.orders.admin_order_details',compact('payment','orderItem'));

    }// End Method 

     public function PendingToConfirm($payment_id){
        Payment::find($payment_id)->update(['status' => 'confirm']);

        $notification = array(
            'message' => 'Order Confrim Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('admin.confirm.order')->with($notification);  


    }// End Method 

      public function AdminConfirmOrder(){
        $payment = Payment::where('status', 'confirm')->orderBy('id','DESC')->get();
        return view('admin.backend.orders.confirm_orders',compact('payment'));
    }//End method


public function InstructorAllOrder() {
    $instructorId = Auth::user()->id;

    $latestOrderItem = Order::select('payment_id', DB::raw('MAX(id) as max_id'))
        ->groupBy('payment_id');

    $orderItem = Order::joinSub($latestOrderItem, 'latest_order', function($join) {
        $join->on('orders.id', '=', 'latest_order.max_id');
    })->orderBy('latest_order.max_id', 'DESC')->get();

    return view('instructor.orders.all_orders', compact('orderItem'));
}//End method 

     public function InstructorOrderDetails($payment_id){

        $payment = Payment::where('id',$payment_id)->first();
        $orderItem = Order::where('payment_id',$payment_id)->orderBy('id','DESC')->get();

        return view('instructor.orders.instructor_order_details',compact('payment','orderItem'));

    }// End Method 

   public function InstructorOrderInvoice($payment_id){

        $payment = Payment::where('id',$payment_id)->first();
        $orderItem = Order::where('payment_id',$payment_id)->orderBy('id','DESC')->get();

        $pdf= PDF::loadView('instructor.orders.order_pdf',compact('payment','orderItem'))->setPaper('a4')->setOption([
            'tempDir' =>public_path(),
            'chroot' =>public_path(),
        ]);
        return $pdf->download('invioce.pdf');

    }// End Method 
    
    public function MyCourse() {
    $id = Auth::user()->id;

    $latestOrder = Order::where('user_id',$id)->select('course_id', DB::raw('MAX(id) as max_id'))
        ->groupBy('course_id');

    $mycourse = Order::joinSub($latestOrder, 'latest_order', function($join) {
        $join->on('orders.id', '=', 'latest_order.max_id');
    })->orderBy('latest_order.max_id', 'DESC')->get();

    return view('frontend.mycourse.my_all_course', compact('mycourse'));
}//End method 

public function CourseView($course_id){
    $id = Auth::user()->id;

    $course = Order::where('course_id', $course_id)
                   ->where('user_id', $id)->first();

    // if (!$course) {
    //     return redirect()->back()->with('error', 'You are not enrolled in this course.');
    // }

    $section = CourseSection::where('course_id', $course_id)
                            ->orderBy('id', 'asc')->get();
        $allquestion = Question::latest()->get();

        return view('frontend.mycourse.course_view',compact('course','section','allquestion'));
}// End method

}

