<?php

// namespace App\Providers;

// use App\Models\SmtpSetting;
// use Illuminate\Support\ServiceProvider;
// use PSpell\Config;


// class AppServiceProvider extends ServiceProvider
// {
//     /**
//      * Register any application services.
//      */
//     public function register(): void
//     {
//         //
//     }

//     /**
//      * Bootstrap any application services.
//      */
//     public function boot(): void
//     {
//         if(\Schema::hasTable('smtp_settings')){
//             $smtpsetting = SmtpSetting::first();
//             if($smtpsetting){
//                 $data =[
//                     'driver' =>$smtpsetting->mailer,
//                     'host' =>$smtpsetting->host,
//                     'port' =>$smtpsetting->port,
//                     'username' =>$smtpsetting->username,
//                     'password' =>$smtpsetting->password,
//                     'encryption' =>$smtpsetting->encryption,
//                     'from'=>[
//                         'address' =>$smtpsetting->form_address,
//                         'name'=> 'Easycourselms'
//                     ]
//                     ];
//                     Config::set('mail',$data);
//             }
//         }
//     }

    
// }



namespace App\Providers;

use App\Models\SmtpSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     if (Schema::hasTable('smtp_settings')) {
    //         $smtpsetting = SmtpSetting::first();
    //         if ($smtpsetting) {
    //             $data = [
    //                 'driver' => $smtpsetting->mailer,
    //                 'host' => $smtpsetting->host,
    //                 'port' => $smtpsetting->port,
    //                 'username' => $smtpsetting->username,
    //                 'password' => $smtpsetting->password,
    //                 'encryption' => $smtpsetting->encryption,
    //                 'from' => [
    //                     'address' => $smtpsetting->from_address,
    //                     'name' => 'Easycourselms',
    //                 ],
    //             ];

    //             Config::set('mail', $data);
    //         }
    //     }
    // }

    public function boot(): void
{
    // ✅ SMTP SETTINGS
    if (Schema::hasTable('smtp_settings')) {
        $smtpsetting = SmtpSetting::first();
        if ($smtpsetting) {
            $data = [
                'driver' => $smtpsetting->mailer,
                'host' => $smtpsetting->host,
                'port' => $smtpsetting->port,
                'username' => $smtpsetting->username,
                'password' => $smtpsetting->password,
                'encryption' => $smtpsetting->encryption,
                'from' => [
                    'address' => $smtpsetting->from_address,
                    'name' => 'Easycourselms',
                ],
            ];

            Config::set('mail', $data);
        }
    }

   
        // ✅ Global Site Settings
        if (Schema::hasTable('site_settings')) {
            $setting = SiteSetting::first(); // Use your SiteSetting model
            View::share('setting', $setting);
        }
}
}
