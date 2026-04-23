<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function StoreReview(Request $request)
    {
        $course = $request->course_id;
        $instructor = $request->instructor_id;
        // Validate the incoming request data
        $request->validate([
           
            'comment' => 'required',
        ]);

        // Store the review in the database (assuming you have a Review model)
           Review::create([
            'user_id' => Auth::id(),
            'course_id' =>$course,
            'instructor_id' => $instructor,
            'rating' => $request->rate,
            'comment' => $request->comment,
            'created_at' => Carbon::now(),
        ]);
        $ntification = array(
            'message' => 'Review Will approve by Admin!',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($ntification);
    }   // End Method

    public function AdminPendingReview(){
        $reviews = Review::where('status',0)->orderBy('id','DESC')->get();
        return view('admin.backend.review.pending_review',compact('reviews'));
    }// End Method
    public function UpdareReviewStatus(Request $request){
        $reviewId = $request-> input('review_id');
        $isChecked = $request->input('is_checked',0);

        $review = Review::find($reviewId);
        if ($review) {
            $review->status = $isChecked;
            $review->save();

            return response()->json(['success' => true, 'message' => 'Review status updated successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Review not found.'], 404);
        }
    }// End Method

    public function AdminActiveReview(){
        $reviews = Review::where('status',1)->orderBy('id','DESC')->get();
        return view('admin.backend.review.active_review',compact('reviews'));
    }// End Method  

    public function InstructorAllReview(){
        $id = Auth::user()->id;
        $reviews = Review::where('instructor_id',$id)->where('status',1)->orderBy('id','DESC')->get();
        return view('instructor.review.active_review',compact('reviews'));
    }// End Method
}
