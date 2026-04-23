<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Facades\Cart;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Orderconfirm;
use App\Models\User;
use Illuminate\Support\Facades\Notification; // ✅ correct
use App\Notifications\OrderComplete;

use function Pest\Laravel\json;

class CartController extends Controller
{
    
public function AddToCart(Request $request, $id)
{
    $course = Course::find($id);
    if(Session::has('coupon')){
        Session::forget('coupon');
    }

    $cartItem = Cart::search(function ($cartItem, $rowId) use ($id) {
        return $cartItem->id === $id;
    });

    if ($cartItem->isNotEmpty()) {
        return response()->json(['error' => 'This course is already added in your cart!']);
    }

    $price = $course->discount_price ?? $course->selling_price;

    Cart::add([
        'id' => $id, // ✅ no quotes
        'name' => $request->course_name, // ✅ no quotes
        'qty' => 1,
        'price' => $price,
        'weight' => 1,
        'options' => [
            'image' => $course->course_image,
            'slug' => $request->course_name_slug,
            'instructor' => $request->instructor
        ],
    ]);

    return response()->json(['success' => 'Successfully added to your cart']);
}



public function CartData()
{
    $carts =Cart::content();;
    $cartTotal = Cart::total();
    $cartQty = Cart::count();

    return response()->json([
        'carts' => $carts,
        'cartTotal' => $cartTotal,
        'cartQty' => $cartQty,
    ]);
}///End method

public function AddMiniCart(){
     $carts =Cart::content();;
    $cartTotal = Cart::total();
    $cartQty = Cart::count();

    return response()->json([
        'carts' => $carts,
        'cartTotal' => $cartTotal,
        'cartQty' => $cartQty,
    ]);
}//End method

public function RemoveMiniCart($rowId){
    Cart::remove($rowId);
    return response()->json(['success'=>'Course Remove From Cart']);
}//End method

 public function MyCart(){
   return view('frontend.mycart.view_mycart');
 }//End method

 public function GetCartCourse(){
     $carts =Cart::content();;
    $cartTotal = Cart::total();
    $cartQty = Cart::count();

    return response()->json([
        'carts' => $carts,
        'cartTotal' => $cartTotal,
        'cartQty' => $cartQty,
    ]);
 }//end method 

 public function cartRemove($rowId){
    Cart::remove($rowId);
     if(Session::has('coupon')){
    $coupon_name= Session::get('coupon')['coupon_name'];
    $coupon=Coupon::where('coupon_name',$coupon_name)->first();

        if (Session::has('coupon')) {
    $coupon_name = Session::get('coupon')['coupon_name'];
    $coupon = Coupon::where('coupon_name', $coupon_name)->first();

    Session::put('coupon', [
        'coupon_name'      => $coupon->coupon_name,
        'coupon_discount'  => $coupon->coupon_discount,
        'discount_amount'  => round(Cart::total() * $coupon->coupon_discount / 100),
        'total_amount'     => round(Cart::total() - Cart::total() * $coupon->coupon_discount / 100),
    ]);
}

    }
    return response()->json(['success'=>'Course Remove From Cart']);
}//End method

public function CouponApply(Request $request)  {
    $coupon = Coupon::where('coupon_name',$request->coupon_name)->where('coupon_validity','>=',Carbon::now()->format('Y-m-d'))->first();
    if($coupon){
         session::put('coupon',[
            'coupon_name'=>$coupon->coupon_name,
            'coupon_discount'=>$coupon->coupon_discount,
            'discount_amount'=> round(Cart::total() * $coupon->coupon_discount/100),
            'total_amount'=>round(Cart::total() - Cart::total()*$coupon->coupon_discount/100),
         ]);
         return response()->json(array(
            'validity'=>true,
            "success"=> "Coupon Applied Successfuly",
         ));
        }
        else{
           return response()->json(['error' => 'Invalid Coupon']);

        }
}//End metod 
public function InsCouponApply(Request $request)
{
    $coupon = Coupon::where('coupon_name', $request->coupon_name)
        ->where('coupon_validity', '>=', Carbon::now()->format('Y-m-d'))
        ->first();

    if ($coupon) {

        if ($coupon->course_id == $request->course_id &&
            $coupon->instructor_id == $request->instructor_id) {

            Session::put('coupon', [
                'coupon_name'     => $coupon->coupon_name,
                'coupon_discount' => $coupon->coupon_discount,
                'discount_amount' => round(Cart::total() * $coupon->coupon_discount / 100),
                'total_amount'    => round(Cart::total() - Cart::total() * $coupon->coupon_discount / 100),
            ]);

            return response()->json([
                'validity' => true,
                'success'  => 'Coupon Applied Successfully',
            ]);

        } else {

            return response()->json([
                'error' => 'Coupon criteria not met for this course and instructor'
            ]);
        }

    } else {

        return response()->json([
            'error' => 'Invalid or expired coupon'
        ]);
    }
}



  public function CouponCalculation()  {
    if(Session::has('coupon')){
        return response()->json(array(
            'subtotal' => Cart::total(),
            'coupon_name'=>Session()->get('coupon')['coupon_name'],
            'coupon_discount'=>Session()->get('coupon')['coupon_discount'],
            'discount_amount'=>Session()->get('coupon')['discount_amount'],
            'total_amount'=>Session()->get('coupon')['total_amount'],
        ));
    }
    else{
        return response()->json(array(
            'total' => Cart::total(),
        ));
    }
  }//End methdod 

  public function CouponRemove()  {
    Session::forget('coupon');
    return response()->json(['success' => 'Coupon Remove Successfully ']);

  }//End method 

 public function CheckoutCreate() {
    if (Auth::check()) {
        if (Cart::total() > 0) {

            $carts     = Cart::content();
            $cartTotal = Cart::total();
            $cartQty   = Cart::count();

            return view('frontend.checkout.checkout_view', compact('carts', 'cartTotal', 'cartQty'));
        } else {
            $notification = [
                'message'    => "Add at least one course to proceed to checkout.",
                'alert-type' => 'error',
            ];
            return redirect()->to('/')->with($notification);
        }
    } else {
        $notification = [
            'message'    => "You need to log in first.",
            'alert-type' => 'error',
        ];
        return redirect()->route('login')->with($notification);
    }
}//End method

    public function Payment(Request $request){
       $user = User::where('role','instructor')->get();
        if(Session::has('coupon')){
            $total_amount = Session::get('coupon')['total_amount'];
        }else{
            $total_amount = round(Cart::total());
        }

        $data = array(); 
                $data['name'] = $request->name;
                $data['email'] = $request->email;
                $data['phone'] = $request->phone;
                $data['address'] = $request->address;
                $data['course_title'] = $request->course_title;
                $cartTotal = Cart::total();
                $carts = Cart::content();
            
        if($request->cash_delivery == 'stripe'){
            return view('frontend.payment.stripe',compact('data','carts','cartTotal'));
        }
        else if($request->cash_delivery == 'handcash'){
        //create new payment
        $data = new Payment();
        $data->name          = $request->name;
        $data->email         = $request->email;
        $data->phone         = $request->phone;
        $data->address       = $request->address;
        $data->cash_delivery = $request->cash_delivery;
        $data->total_amount  = $total_amount;
        $data->payment_type  = 'Direct Payment';

        $data->invoice_no    = 'EOS' . mt_rand(10000000, 99999999);
        $data->order_date    = Carbon::now()->format('d F Y');
        $data->order_month   = Carbon::now()->format('F');
        $data->order_year    = Carbon::now()->format('Y');
        $data->status        = 'pending';
        $data->created_at    = Carbon::now();

        $data->save();

        foreach($request->course_title as $key => $course_title){
            $existingOrder = Order::where('user_id',Auth::user()->id)->where('course_id',$request->course_id[$key])->first();
            if($existingOrder){
                $notification = [
                'message'    => "You have already enrolled in this course.",
                'alert-type' => 'error',
            ];
            return redirect()->back()->with($notification);
            }
            $order = new Order();
            $order->payment_id = $data->id;
            $order->user_id = Auth::user()->id;
            $order->course_id = $request->course_id[$key];
            $order->instructor_id = $request->instructor_id[$key];
            $order->course_title = $request->course_title[$key];
            $order->price = $request->price[$key];
            $order->save();
            }//end foreach
            $request->session()->forget('cart');

        $paymentId = $data->id;

    // Start send email to students //
    $sendmail = Payment::find($paymentId);

    $data = [
        'invoice_no' => $request->invoice_no,
        'amount' => $total_amount,
        'name' => $sendmail->name,
        'email' => $sendmail->email,
    ];

    Mail::to($request->email)->send(new Orderconfirm($data));
    // End send email to students //

    // Send Notification
   
Notification::send($user, new OrderComplete($request->name));


    $notification = [
                'message'    => "Cash Payment submited Successfully",
                'alert-type' => 'success',
            ];
            return redirect()->route('index')->with($notification);

        }//end elseif
        
        
    }//End method 



  public function StripeOrder(Request $request){
        if (Session::has('coupon')) {
            $total_amount = Session::get('coupon')['total_amount'];
         }else {
             $total_amount = round(Cart::total());
         }

         \Stripe\Stripe::setApiKey('sk_test_51IUTWzALc6pn5BvMjaRW9STAvY4pLiq1dNViHoh5KtqJc9Bx7d4WKlCcEdHOJdg3gCcC2F19cDxUmCBJekGSZXte00RN2Fc4vm');

         $token = $_POST['stripeToken'];

         $charge = \Stripe\Charge::create([
            'amount' => $total_amount*100, 
            'currency' => 'usd',
            'description' => 'Lms',
            'source' => $token,
            'metadata' => ['order_id' => '3434'],
         ]);

         $order_id = Payment::insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'total_amount' => $total_amount,
            'payment_type' => 'Stripe',
            'invoice_no' => 'EOS' . mt_rand(10000000, 99999999),
            'order_date' => Carbon::now()->format('d F Y'),
            'order_month' => Carbon::now()->format('F'),
            'order_year' => Carbon::now()->format('Y'),
            'status' => 'pending',
            'created_at' => Carbon::now(), 

         ]);

         $carts = Cart::content();
         foreach ($carts as $cart) {
            Order::insert([
                'payment_id' => $order_id,
                'user_id' => Auth::user()->id,
                'course_id' => $cart->id,
                'instructor_id' => $cart->options->instructor,
                'course_title' => $cart->options->name,
                'price' => $cart->price,
            ]);
         }// end foreach 

         if (Session::has('coupon')) {
            Session::forget('coupon');
         }
         Cart::destroy();

         $notification = array(
            'message' => 'Stripe Payment Submit Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('index')->with($notification); 

    }// End Method 

public function BuyToCart(Request $request,$id)
{
    $course = Course::find($id);
    if(Session::has('coupon')){
        Session::forget('coupon');
    }

    $cartItem = Cart::search(function ($cartItem, $rowId) use ($id) {
        return $cartItem->id === $id;
    });

    if ($cartItem->isNotEmpty()) {
        return response()->json(['error' => 'This course is already added in your cart!']);
    }

    $price = $course->discount_price ?? $course->selling_price;

    Cart::add([
        'id' => $id, // ✅ no quotes
        'name' => $request->course_name, // ✅ no quotes
        'qty' => 1,
        'price' => $price,
        'weight' => 1,
        'options' => [
        'image' => $course->course_image,
        'slug' => $request->course_name_slug,
        'instructor' => $request->instructor
        ],
    ]);

    return response()->json(['success' => 'Successfully added to your cart']);


}//End method


public function MarkAsRead(Request $request, $notificationId)
{
    $user = Auth::user();
    $notification = $user->notifications()->where('id', $notificationId)->first();

    if ($notification) {
        $notification->markAsRead();
    }

    return response()->json([
        'count' => $user->unreadNotifications()->count()
    ]);
}//End Method 


}
