<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

use function Pest\Laravel\get;

class CategoryController extends Controller
{
public function AllCategory()  {
  $category=Category::latest()->get();
  return view('admin.backend.category.all_category',compact('category'));
    
}//end method

public function AddCategory()  {
     return view('admin.backend.category.add_category');
}//end method
   
 public function StoreCategory(Request $request){
        if($request->file('image')){
      $manager = new ImageManager(new Driver());
      $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
      $img=$manager->read($request->file('image'));

      $img= $img->resize(370,246);
      $img->toJpeg(80)->save(base_path('public/upload/category/'.$name_gen));
         $save_url = 'upload/category/'.$name_gen;
     
        Category::insert([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ','-',$request->category_name)),
            'image' => $save_url,        

        ]);
}
        $notification = array(
            'message' => 'Category Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.category')->with($notification);  
        
    }// End Method 


    public function EditCategory($id)
{
    $category = Category::findOrFail($id);
    return view('admin.backend.category.edit_category', compact('category'));
}// end of method


    
public function UpdateCategory(Request $request)
{
    $cat_id = $request->id;

    // create image manager instance (new syntax)
    $manager = new ImageManager(new Driver());

    if ($request->file('image')) {

        // old image delete (optional)
        $category = Category::findOrFail($cat_id);
        if (file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $image = $request->file('image');  
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

        // ✅ read and resize with Intervention v3
        $img = $manager->read($image)->resize(370, 246);

        // ✅ Save to public/upload/category/
        $path = public_path('upload/category/');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $img->toJpeg(80)->save($path . $name_gen);
        $save_url = 'upload/category/' . $name_gen;

        // ✅ update database
        $category->update([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
            'image' => $save_url,
        ]);

        $notification = [
            'message' => 'Category updated with image successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.category')->with($notification);

    } else {
        Category::findOrFail($cat_id)->update([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
        ]);

        $notification = [
            'message' => 'Category updated without image successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.category')->with($notification);
    }
}//End method

  public function DeleteCategory($id)  {
    $item=Category::find($id);
    $img=$item->image;
    unlink($img);
    Category::find($id)->delete();

     $notification = [
            'message' => 'Category deleted successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    
  }

  //////// All Yoour Sub category methods ////
         
 public function AllSubCategory()
{
    $subcategory = SubCategory::with('category')->latest()->get();
    return view('admin.backend.subcategory.all_subcategory', compact('subcategory'));
}

public function AddSubCategory(){

        $category = Category::latest()->get();
        return view('admin.backend.subcategory.add_subcategory',compact('category'));

    }// End Method 


    public function StoreSubCategory(Request $request){ 

        SubCategory::insert([
            'category_id' => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
            'subcategory_slug' => strtolower(str_replace(' ','-',$request->subcategory_name)), 

        ]);

        $notification = array(
            'message' => 'SubCategory Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.subcategory')->with($notification);  

    }// End Method 
 public function EditSubCategory($id){

        $category = Category::latest()->get();
        $subcategory = SubCategory::find($id);
        return view('admin.backend.subcategory.edit_subcategory',compact('category','subcategory'));

    }// End Method


    public function UpdateSubCategory(Request $request){ 

        $subcat_id = $request->id;

        SubCategory::find($subcat_id)->update([
            'category_id' => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
            'subcategory_slug' => strtolower(str_replace(' ','-',$request->subcategory_name)), 

        ]);

        $notification = array(
            'message' => 'SubCategory Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.subcategory')->with($notification);  

    }// End Method 


    public function DeleteSubCategory($id){

        SubCategory::find($id)->delete();

        $notification = array(
            'message' => 'SubCategory Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }// End Method 

    public function getSubcategories($category_id)
    {
        $subcategories = Subcategory::where('category_id', $category_id)->get();
        return response()->json($subcategories);
    }

}


