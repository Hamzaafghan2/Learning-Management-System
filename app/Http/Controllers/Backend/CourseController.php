<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\Course_goal;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth; // ✅ Correct Auth import
use Carbon\Carbon;

use function Pest\Laravel\get;

class CourseController extends Controller
{
    public function AllCourse()
    {
        $id = Auth::user()->id;
        $courses = Course::where('instructor_id', $id)
                         ->orderBy('id', 'desc')
                         ->get();

        return view('instructor.courses.all_course', compact('courses'));
    }//End Method

    public function AddCourse()  {
        $categories=Category::latest()->get();
        return view('instructor.courses.add_course', compact('categories'));
        
    }//End Method

    public function GetSubCategory($category_id)
{
    $subcat = SubCategory::where('category_id', $category_id)
        ->orderBy('subcategory_name', 'ASC')
        ->get();

    return response()->json($subcat);
}

    // public function GetSubCategory($category_id)  {
    //     $subcat = SubCategory::where('category_id',$category_id)->orderBy('subcategory_name','ASC')->get();
    //     return json_encode($subcat);
    // }// End method

public function StoreCourse(Request $request)
{
    // ✅ Step 1: Validation
    $request->validate([
     
        'video'            => 'required|mimes:mp4,avi,mov|max:20000',
        // 'course_goals'     => 'nullable|array',
        // 'course_goals.*'   => 'nullable|string|max:255',
    ]);

    // ✅ Step 2: Handle Image Upload using Intervention v3
    $manager = new ImageManager(new Driver());
    $image = $request->file('course_image');
    $imageName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

    // Resize & save to /public/upload/course/thambnail/
    $img = $manager->read($image)->resize(370, 246);
    $img->toJpeg(80)->save(public_path('upload/course/thambnail/' . $imageName));

    $save_url = 'upload/course/thambnail/' . $imageName;

    // ✅ Step 3: Handle Video Upload
    $video = $request->file('video');
    $videoName = time() . '.' . $video->getClientOriginalExtension();
    $video->move(public_path('upload/course/video/'), $videoName);

    $save_video = 'upload/course/video/' . $videoName;

    // ✅ Step 4: Insert Course Record
    $course_id = Course::insertGetId([
        'category_id'     => $request->category_id,
        'subcategory_id'  => $request->subcategory_id,
        'instructor_id'   => Auth::user()->id,
        'course_title'    => $request->course_title,
        'course_name'     => $request->course_name,
        'course_name_slug'=> strtolower(str_replace(' ', '-', $request->course_name)),
        'description'     => $request->description,
        'video'           => $save_video,
        'label'           => $request->label,
        'duration'        => $request->duration,
        'resources'       => $request->resources,
        'certificate'     => $request->certificate,
        'selling_price'   => $request->selling_price,
        'discount_price'  => $request->discount_price,
        'prerequisites'   => $request->prerequisites,
        'bestseller'      => $request->bestseller,
        'featured'        => $request->featured,
        'highestrated'    => $request->highestrated,
        'status'          => 1,
        'course_image'    => $save_url,
        'created_at'      => Carbon::now(),
    ]);

    /// Course Goals Add Form 

        $goles = Count($request->course_goals);
        if ($goles != NULL) {
            for ($i=0; $i < $goles; $i++) { 
                $gcount = new Course_goal();
                $gcount->course_id = $course_id;
                $gcount->goal_name = $request->course_goals[$i];
                $gcount->save();
            }
        }
        /// End Course Goals Add Form 

        $notification = array(
            'message' => 'Course Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.course')->with($notification);  
}//End method

public function EditCourse($id){
 $course=Course::find($id);
 $goals=Course_goal::where('course_id',$id)->get();
 $categories= Category::latest()->get();
 $subcategories= SubCategory::latest()->get();
 return view('instructor.courses.edit_course',compact('course','categories','subcategories','goals'));
}//End method



public function UpdateCourse(Request $request)
{
    $course = Course::findOrFail($request->course_id);

    $course->update([
        'course_name' => $request->course_name,
        'course_title' => $request->course_title,
        'selling_price' => $request->selling_price,
        'discount_price' => $request->discount_price,
        'duration' => $request->duration,
        'resources' => $request->resources,
        'certificate' => $request->certificate,
        'label' => $request->label,
        'prerequisites' => $request->prerequisites,
        'bestseller' => $request->bestseller,
        'featured' => $request->featured,
        'highestrated' => $request->highestrated,
        'description' => $request->description,
    ]);

    return redirect()->route('all.course')->with([
        'message' => 'Course Updated Successfully',
        'alert-type' => 'success'
    ]);
}//End method

public function UpdateCourseImage(Request $request)  {
    $course_id=$request->id;
    $oldImage=$request->old_img;
    
     $manager = new ImageManager(new Driver());
    $image = $request->file('course_image');
    $imageName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

    // Resize & save to /public/upload/course/thambnail/
    $img = $manager->read($image)->resize(370, 246);
    $img->toJpeg(80)->save(public_path('upload/course/thambnail/' . $imageName));

    $save_url = 'upload/course/thambnail/' . $imageName;
    if(file_exists($oldImage)){
        unlink($oldImage);
    }
    Course::find($course_id)->update([
        'course_image' => $save_url,
        'updated_at' =>Carbon::now(),
    ]);
    
        $notification = array(
            'message' => 'Course Image updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);  
}//End method

public function UpdateCourseVideo(Request $request)  {
     $course_id=$request->vid;
    $oldVideo=$request->old_vid;
    
     $video = $request->file('video');
    $videoName = time() . '.' . $video->getClientOriginalExtension();
    $video->move(public_path('upload/course/video/'), $videoName);

    $save_video = 'upload/course/video/' . $videoName;

    if(file_exists($oldVideo)){
        unlink($oldVideo);
    }
    Course::find($course_id)->update([
        'video' => $save_video,
        'updated_at' =>Carbon::now(),
    ]);
    
        $notification = array(
            'message' => 'Course video updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    
}//End method 
        public function UpdateCourseGoal(Request $request){
            $cid=$request->id;
            if($request->course_goals==NULL){
                return redirect()->back();
            }else{
                Course_goal::where('course_id',$cid)->delete();

                   /// Course Goals Add Form 

        $goles = Count($request->course_goals);
       
            for ($i=0; $i < $goles; $i++) { 
                $gcount = new Course_goal();
                $gcount->course_id = $cid;
                $gcount->goal_name = $request->course_goals[$i];
                $gcount->save();
        }/// End Course Goals Add Form 
            } //end else
            
        $notification = array(
            'message' => 'Course Goals updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
        }//End method

        public function DeleteCourse($id)  {
       $course=Course::find($id);
       unlink($course->course_image);
       unlink($course->video);
       Course::find($id)->delete();

       $goalsDate=Course_goal::where('course_id',$id)->get();
          
       foreach ($goalsDate as $item) {

       $item->goal_name;
       Course_goal::where('course_id',$id)->delete();
       }
        $notification = array(
            'message' => 'Course Goals updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
        }//End method

        public function AddCourseLecture($id) 
        {
            $course = Course::find($id);
            $section=CourseSection::where('course_id',$id)->latest()->get();
            return view('instructor.courses.section.add_course_lecture',compact('course','section'));
            
        }//End method 
         public function AddCourseSection(Request $request)  {

            $cid = $request->id;
            CourseSection::insert([
                'course_id'=>$cid,                
                'section_title'=>$request->section_title,                
            ]);
            $notification = array(
            'message' => 'Course section inserted Successfully',
            'alert-type' => 'success'
        );
         return redirect()->back()->with($notification);
         }///End method
      
        //  public function SaveLecture(Request $request)  {
        //     $lecture = new CourseLecture();
        //     $lecture->course_id = $request->course_id;
        //     $lecture->section_id = $request->section_id;
        //     $lecture->lecture_title = $request->lecture_title;
        //     $lecture->url = $request->lecture_url;
        //    $lecture->content = $request->content;           
        //     $lecture->save();            
        //       return response()->json((['success'=>'lecture saved successfuly']));
        //  }//End method

      public function SaveLecture(Request $request)
{
    $data = json_decode($request->getContent(), true);
    // optional validation...
    $lecture = new CourseLecture();
    $lecture->course_id = $data['course_id'];
    $lecture->section_id = $data['section_id'];
    $lecture->lecture_title = $data['lecture_title'];
    $lecture->url = $data['lecture_url'];
    $lecture->content = $data['content'];
    $lecture->save();

    return response()->json(['success' => 'Lecture saved successfully']);
}///End method
  public function EditLecture($id)  {
    $clecture = CourseLecture::find($id);

    return view('instructor.courses.lecture.edit_course_lecture',compact('clecture'));
    
  } //end metho

public function UpdateCourseLecture(Request $request)
{
    $lid = $request->id;

    CourseLecture::findOrFail($lid)->update([
        'lecture_title' => $request->lecture_title,
        'url' => $request->url,
        'content' => $request->content,
    ]);

    $notification = [
        'message' => 'Course Lecture Updated Successfully',
        'alert-type' => 'success',
    ];

    return redirect()->back()->with($notification);
}//End method
  
public function DeleteLecture($id)  {
  CourseLecture::find($id)->delete();
    $notification = [
        'message' => 'Course Lecture deleted Successfully',
        'alert-type' => 'success',
    ];

    return redirect()->back()->with($notification);
}//End method

public function DeleteSection($id)  {

   $section= CourseSection::find($id);
   ///delete releated section 
   $section->lectures()->delete();
   
   $section->delete();
     $notification = [
        'message' => 'Course section deleted Successfully',
        'alert-type' => 'success',
    ];

    return redirect()->back()->with($notification);
}//End method
}