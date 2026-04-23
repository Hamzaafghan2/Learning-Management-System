<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function UserQuestion(Request $request){
        $course_id = $request->course_id;
        $instructor_id = $request->instructor_id;

        Question::insert([
            'course_id' => $course_id,
            'user_id'=> Auth::user()->id,
            'instructor_id' =>$instructor_id,
            'subject' => $request-> subject,
            'question' => $request-> question, 
            'created_at' => Carbon::now(),
        ]);
         $notification = array(
            'message' => 'Question submited Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification); 

    }// End Method
    public function InstructorAllQuestion(){
        $id = Auth::user()->id;
        $question = Question::where('instructor_id',$id)->where('parent_id',NULL)->orderBy('id','DESC')->get();
        return view('instructor.question.all_question',compact('question'));
    }// End Method
    public function QuestionDetails($id){
         $question = Question::find($id);
         $replay = Question::where('parent_id',$id)->orderBy('id','asc')->get();
         return view('instructor.question.question_details',compact('question','replay'));
    }// End Method
    public function InstructorRepaly(Request $request){
        $que_id = $request->qid;
        $course_id = $request->course_id;
        $user_id = $request->user_id;
        $instructor_id = $request->instructor_id;
        
        Question::insert([
            'course_id'=> $course_id ,
            'user_id'=> $user_id,
            'instructor_id'=> $instructor_id,
            'parent_id'=> $que_id,
            'question'=> $request->question,
        ]);
         $notification = array(
            'message' => 'Repaly Sent Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('instructor.all.question')->with($notification);
    }// End Method
}
