<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Interevention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class BlogController extends Controller
{
    public function AllBlogCategory(){
        $category = BlogCategory::latest()->get();
        return view('admin.backend.blogcategory.blog_category', compact('category'));
    }//end method

    public function BlogCategoryStore(Request $request){
        BlogCategory::insert([
            'category_name' => $request->category_name,
             'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
        ]);
        $notification = array(
            'message' => 'Blog Category Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }//end method

    public function EditBlogCategory($id){

    $categories = BlogCategory::find($id);
    return response()->json($categories);

   }// End Method 

    public function UpdateBlogCategory(Request $request){
        $cat_id = $request->cat_id;
        BlogCategory::find($cat_id)->update([
            'category_name' => $request->category_name,
             'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
        ]);
        $notification = array(
            'message' => 'Blog Category Update Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }//end method

    public function DeleteBlogCategory($id){
        BlogCategory::find($id)->delete();
        $notification = array(
            'message' => 'Blog Category Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }//end method

    //=================== Blog Post All Methods ==================//
      public function BlogPost(){
    $post = BlogPost::latest()->get();
    return view('admin.backend.post.all_post',compact('post'));
   }// End Method 

    public function AddBlogPost(){

    $blogcat = BlogCategory::latest()->get();
    return view('admin.backend.post.add_post',compact('blogcat'));

   }// End Method 

 public function StoreBlogPost(Request $request)
{
    $request->validate([
        'blogcat_id' => 'required',
        'post_title' => 'required',
        'long_descp' => 'required',
        'post_image' => 'required|image'
    ]);

    $manager = new ImageManager(new Driver());

    $image = $request->file('post_image');
    $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

    $img = $manager->read($image);
    $img->resize(370, 247);
    $img->save(public_path('upload/post/' . $name_gen));

    $save_url = 'upload/post/' . $name_gen;

    BlogPost::create([
        'blogcat_id' => $request->blogcat_id,
        'post_title' => $request->post_title,
        'post_slug' => str()->slug($request->post_title),
        'long_descp' => $request->long_descp,
        'post_tags' => $request->post_tags,
        'post_image' => $save_url,
        'created_at' => Carbon::now(),
    ]);

    return redirect()->route('blog.post')
        ->with(['message' => 'Blog Post Inserted Successfully', 'alert-type' => 'success']);
}//end method

    public function EditBlogPost($id){
    
        $blogcat = BlogCategory::latest()->get();
        $post = BlogPost::find($id);
        
        return view('admin.backend.post.edit_post',compact('post','blogcat'));
    
    }// End Method
   

public function UpdateBlogPost(Request $request)
{
    $post_id = $request->id;

    // Get the existing post
    $post = BlogPost::findOrFail($post_id);

    // If new image uploaded
    if ($request->file('post_image')) {

        $manager = new ImageManager(new Driver());

        $image = $request->file('post_image');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

        // Resize and save
        $img = $manager->read($image);
        $img->resize(370, 247);
        $img->save(public_path('upload/post/' . $name_gen));

        $save_url = 'upload/post/' . $name_gen;

        // Delete old image
        if (file_exists(public_path($post->post_image))) {
            unlink(public_path($post->post_image));
        }

        // Update WITH image
        $post->update([
            'blogcat_id' => $request->blogcat_id,
            'post_title' => $request->post_title,
            'post_slug' => str()->slug($request->post_title),
            'long_descp' => $request->long_descp,
            'post_tags' => $request->post_tags,
            'post_image' => $save_url,
            'updated_at' => Carbon::now(),
        ]);

    } else {

        // Update WITHOUT image
        $post->update([
            'blogcat_id' => $request->blogcat_id,
            'post_title' => $request->post_title,
            'post_slug' => str()->slug($request->post_title),
            'long_descp' => $request->long_descp,
            'post_tags' => $request->post_tags,
            'updated_at' => Carbon::now(),
        ]);
    }

    return redirect()->route('blog.post')->with([
        'message' => 'Blog Post Updated Successfully',
        'alert-type' => 'success'
    ]);
}//end method 
 
public function DeleteBlogPost($id){
    $item = BlogPost::find($id);
    $img = $item->post_image;
    unlink($img);

    BlogPost::find($id)->delete();

        $notification = array(
            'message' => 'Blog Post Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

}// End Method 

public function BlogDetails($slug){
    $blog = BlogPost::where('post_slug',$slug)->first();
    $tags = $blog->post_tags;
    $all_tags = explode(',',$tags);
    $bcategory = BlogCategory::latest()->get();
    $post = BlogPost::latest()->limit(3)->get();
    return view('frontend.blog.blog_details',compact('blog','all_tags','bcategory','post'));
}//end method

public function BlogCatList($id){

    $blog = BlogPost::where('blogcat_id',$id)->get();
    $breadcat = BlogCategory::where('id',$id)->first();
    $bcategory = BlogCategory::latest()->get();
    $post = BlogPost::latest()->limit(3)->get();
    return view('frontend.blog.blog_cat_list',compact('blog','breadcat','bcategory','post'));

}// End Method 
public function BlogList(){
    $blog = BlogPost::latest()->paginate(2);
    $bcategory = BlogCategory::latest()->get();
    $post =BlogPost::latest()->limit(3)->get();
    return view('frontend.blog.blog_list',compact('blog','bcategory','post'));
}// end method 
}

