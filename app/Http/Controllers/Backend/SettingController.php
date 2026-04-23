<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SiteSetting;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SettingController extends Controller
{
    // public function SmtpSetting()
    // {
    //     $smtp = SmtpSetting::find(1);
    //     return view('admin.backend.setting.smtp_update', compact('smtp'));
    // } // End method 

    public function SmtpSetting()
{
    $smtp = SmtpSetting::first();

    if (!$smtp) {
        $smtp = SmtpSetting::create([
            'mailer' => 'smtp',
            'host' => '',
            'port' => 587,
            'username' => '',
            'password' => '',
            'encryption' => 'tls',
            'from_address' => '',
        ]);
    }

    return view('admin.backend.setting.smtp_update', compact('smtp'));
}//End method 

    public function SmtpUpdate(Request $request)
    {
        $request->validate([
            'mailer' => 'required|string',
            'host' => 'required|string',
            'port' => 'required|numeric',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'nullable|string',
            'from_address' => 'required|email',
        ]);

        $smtp_id = $request->id;

        // SmtpSetting::find($smtp_id)->update([
        //     'mailer' => $request->mailer,
        //     'host' => $request->host,
        //     'port' => $request->port,
        //     'username' => $request->username,
        //     'password' => $request->password,
        //     'encryption' => $request->encryption,
        //     'from_address' => $request->from_address,
        // ]);

        $smtp = SmtpSetting::findOrFail($smtp_id);
        $smtp->update([
            'mailer' => $request->mailer,
            'host' => $request->host,
            'port' => $request->port,
            'username' => $request->username,
            'password' => $request->password,
            'encryption' => $request->encryption,
            'from_address' => $request->from_address,
        ]);
                $notification = [
            'message' => "SMTP Setting Updated Successfully",
            'alert-type' => 'success',
        ];

        return back()->with($notification);
    } // End method 

    public function SiteSetting(){
        $site = SiteSetting::find(1);
        return view ('admin.backend.site.site_update',compact('site'));
    }//End Method
   
    // public function UpdateSetting(Request $request){

    //     $site_id = $request->id;

    // // create image manager instance (new syntax)
    // $manager = new ImageManager(new Driver());

    // if ($request->file('logo')) {

    //     // old image delete (optional)
    //     $category = SiteSetting::findOrFail($site_id);
    //     if (file_exists(public_path($category->image))) {
    //         unlink(public_path($category->logo));
    //     }

    //     $image = $request->file('logo');  
    //     $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

    //     // ✅ read and resize with Intervention v3
    //     $img = $manager->read($image)->resize(140, 41);

    //     // ✅ Save to public/upload/category/
    //     $path = public_path('upload/logo/');
    //     if (!file_exists($path)) {
    //         mkdir($path, 0777, true);
    //     }

    //     $img->toJpeg(80)->save($path . $name_gen);
    //     $save_url = 'upload/logo/' . $name_gen;

    //     // ✅ update database
    //     $category->update([

    //        'phone' => $request->phone,            
    //        'email' => $request->email,            
    //        'address' => $request->address,            
    //        'facebook' => $request->facebook,            
    //        'twitter' => $request->twitter,            
    //        'copyright' => $request->copyright,      
    //         'logo' => $save_url,
    //     ]);

    //     $notification = [
    //         'message' => 'Site Setting updated with image successfully',
    //         'alert-type' => 'success',
    //     ];

    //     return redirect()->back()->with($notification);

    // } else {
    //     SiteSetting::findOrFail($site_id)->update([
    //        'phone' => $request->phone,            
    //        'email' => $request->email,            
    //        'address' => $request->address,            
    //        'facebook' => $request->facebook,            
    //        'twitter' => $request->twitter,            
    //        'copyright' => $request->copyright,      
    //     ]);

    //     $notification = [
    //         'message' => 'Site Setting updated without image successfully',
    //         'alert-type' => 'success',
    //     ];

    //     return redirect()->back()->with($notification);
    // }
    // }

    public function UpdateSetting(Request $request)
{
    $site_id = $request->id;

    $manager = new ImageManager(new Driver());

    $category = SiteSetting::findOrFail($site_id);

    if ($request->file('logo')) {

        // Delete old logo if exists
        if ($category->logo && file_exists(public_path($category->logo))) {
            unlink(public_path($category->logo));
        }

        // Upload new logo
        $image = $request->file('logo');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

        // Resize image (Intervention 3)
        $img = $manager->read($image)->resize(140, 41);

        // Create folder if missing
        $path = public_path('upload/logo/');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Save image
        $img->toJpeg(80)->save($path . $name_gen);

        // Image path for DB
        $save_url = 'upload/logo/' . $name_gen;

        // Update DB
        $category->update([
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'copyright' => $request->copyright,
            'logo' => $save_url,
        ]);

        return redirect()->back()->with([
            'message' => 'Site Setting updated with image successfully',
            'alert-type' => 'success'
        ]);
    }

    // Update WITHOUT logo
    $category->update([
        'phone' => $request->phone,
        'email' => $request->email,
        'address' => $request->address,
        'facebook' => $request->facebook,
        'twitter' => $request->twitter,
        'copyright' => $request->copyright,
    ]);

    return redirect()->back()->with([
        'message' => 'Site Setting updated without image successfully',
        'alert-type' => 'success'
    ]);
}

}


