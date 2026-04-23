<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Backend\ActiveUserController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ChatController;
use App\Http\Controllers\Backend\CourseController;
use App\Http\Controllers\Backend\QuestionController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SubCategoryController; // Only if exists
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Frontend\WishListController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\ReviewController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\ExcelController;
use FontLib\Table\Type\name;

// =====================================================
// Public Routes
// =====================================================

Route::get('/', [UserController::class, 'index'])->name('index');

Route::get('/course/details/{id}/{slug}', [IndexController::class, 'CourseDetails']);
Route::get('/category/{id}/{slug}', [IndexController::class, 'CategoryCourse']);
Route::get('/subcategory/{id}/{slug}', [IndexController::class, 'SubCategoryCourse']);
Route::get('/instructor/details/{id}', [IndexController::class, 'InstructorDetails'])
    ->name('instructor.details');

Route::post('/add-to-wishlist/{course_id}', [WishListController::class, 'AddToWishList']);
Route::post('/cart/data/store/{id}', [CartController::class, 'AddToCart'])
    ->name('cart.data.store');
Route::post('/buy/data/store/{id}', [CartController::class, 'BuyToCart']);

Route::get('/course/mini/cart', [CartController::class, 'AddMiniCart']);
Route::get('/minicart/course/remove/{rowId}', [CartController::class, 'RemoveMiniCart']);

Route::get('/cart/data', [CartController::class, 'CartData']);

Route::get('/instructor/login', [InstructorController::class, 'instructorLogin']) ->middleware('guest.role:instructor')
    ->name('instructor.login');

Route::get('/admin/login', [AdminController::class, 'adminLogin']) ->middleware('guest.role:admin')
    ->name('admin.login');

Route::get('/become/instructor', [AdminController::class, 'BecomeIstructor'])
    ->name('become.instructor');

// =====================================================
// User Authenticated Routes
// =====================================================

Route::middleware(['auth', 'role:user'])->group(function () {

    Route::get('/dashboard', function () {
        return view('frontend.dashboard.index');
    })->name('dashboard');

    // User Profile
    Route::controller(UserController::class)->group(function () {
        Route::get('/user/profile', 'UserProfile')->name('user.profile');
        Route::post('/user/profile/update', 'UserProfileUpdate')->name('user.profile.update');
        Route::get('/user/logout', 'UserLogout')->name('user.logout');

        Route::get('/user/change/password', 'UserChangePassword')->name('user.change.password');
        Route::post('/user/password/update', 'UserPasswordUpdate')->name('user.password.update'); 
         Route::get('/live/chat', 'LiveChat')->name('live.chat');
    });

    // Wishlist 
    Route::controller(WishListController::class)->group(function () {
        Route::get('/user/wishlist', 'AllWishist')->name('user.wishlist');
        Route::get('/get-wishlist-course', 'GetWishlistCourse');
        Route::get('/wishlist-remove/{id}', 'RemoveWishlist');
    });

    // User Orders / My Courses
    Route::controller(OrderController::class)->group(function () {
        Route::get('/my/course', 'MyCourse')->name('my.course');
        Route::get('/course/view/{course_id}', 'CourseView')->name('course.view');
    });

    // User Questions
    Route::controller(QuestionController::class)->group(function () {
        Route::post('/user/question', 'UserQuestion')->name('user.question');
    });
});

require __DIR__.'/auth.php';


// =====================================================
// Admin Routes (Authenticated)
// =====================================================

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/dashboard', 'AdminDashboard')->name('admin.admin_dashboard');
        Route::post('/admin/logout', 'adminLogout')->name('admin.logout');

        Route::get('/admin/profile', 'AdminProfile')->name('admin.profile');
        Route::post('/admin/profile/store', 'AdminProfileStore')->name('admin.profile.store');

        Route::get('/admin/change/password', 'AdminChangePassword')->name('admin.change.password');
        Route::post('/admin/password/update', 'AdminPasswordUpdate')->name('admin.password.update');

        Route::get('/all/instructor', 'AllInstructor')->name('all.instructor');
        Route::post('/update/user/status', 'UpdateUserStatus')->name('update.user.status');
    });

    // Admin Category Routes
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/all/category', 'AllCategory')->name('all.category');
        Route::get('/add/category', 'AddCategory')->name('add.category');
        Route::post('/store/category', 'StoreCategory')->name('store.category');

        Route::get('/edit/category/{id}', 'EditCategory')->name('edit.category');
        Route::post('/update/category', 'UpdateCategory')->name('update.category');
        Route::get('/delete/category/{id}', 'DeleteCategory')->name('delete.category');

        // Subcategory
        Route::get('/all/subcategory', 'AllSubCategory')->name('all.subcategory');
        Route::get('/add/subcategory', 'AddSubCategory')->name('add.subcategory');
        Route::post('/store/subcategory', 'StoreSubCategory')->name('store.subcategory');

        Route::get('/edit/subcategory/{id}', 'EditSubCategory')->name('edit.subcategory');
        Route::post('/update/subcategory', 'UpdateSubCategory')->name('update.subcategory');
        Route::get('/delete/subcategory/{id}', 'DeleteSubCategory')->name('delete.subcategory');
       
    });

    // Admin Courses Management
    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/all/course', 'AdminAllCourse')->name('admin.all.course');
        Route::post('/update/course/status', 'UpdateCourseStatus')->name('update.course.status');
        Route::get('/admin/course/details/{id}', 'AdminCourseDetails')->name('admin.course.details');
    });

    // Coupons 
    Route::controller(CouponController::class)->group(function () {
        Route::get('/admin/all/coupon', 'AdminAllCoupon')->name('admin.all.coupon');
        Route::get('/admin/add/coupon', 'AdminAddCoupon')->name('admin.add.coupon');
        Route::post('/admin/store/coupon', 'AdminStoreCoupon')->name('admin.store.coupon');
        Route::get('/admin/edit/coupon/{id}', 'AdminEditCoupon')->name('admin.edit.coupon');
        Route::post('/admin/update/coupon', 'AdminUpdateCoupon')->name('admin.update.coupon');
        Route::get('/admin/delete/coupon/{id}', 'AdminDeleteCoupon')->name('admin.delete.coupon');
    });

    // SMTP Settings
    Route::controller(SettingController::class)->group(function () {
        Route::get('/smtp/setting', 'SmtpSetting')->name('smtp.setting');
        Route::post('/update/smtp', 'SmtpUpdate')->name('update.smtp');
    });

     // Site  Settings
    Route::controller(SettingController::class)->group(function () {
        Route::get('/site/setting', 'SiteSetting')->name('site.setting');
        Route::post('/update/site', 'UpdateSetting')->name('update.site');
       
    });

    // Admin All Orders Route
    Route::controller(OrderController::class)->group(function () {
        Route::get('/admin/pending/order', 'AdminPendingOrder')->name('admin.pending.order');
        Route::get('/admin/order/details/{id}', 'AdminOrderDetails')->name('admin.order.details');

        Route::get('/pending-confrim/{id}', 'PendingToConfirm')->name('pending-confrim');
        Route::get('/admin/confirm/order', 'AdminConfirmOrder')->name('admin.confirm.order');
    });

    // Admin Reports all Route
    Route::controller(ReportController::class)->group(function () {
        Route::get('/report/view', 'ReportView')->name('report.view');
        Route::post('/search/by/date', 'SearchByDate')->name('search.by.date');
        Route::post('/search/by/month', 'SearchByMonth')->name('search.by.month');
        Route::post('/search/by/year', 'SearchByYear')->name('search.by.year');
        
    });
    /// Admin Review ALL Routes
    Route::controller(ReviewController::class)->group(function () {
        Route::get('/admin/pending/review', 'AdminPendingReview')->name('admin.pending.review');

        Route::post('update/review/status', 'UpdareReviewStatus')->name('update.review.status');
        Route::get('admin/active/review', 'AdminActiveReview')->name('admin.active.review');
       
    });
    // Admin Active User and instructor ALL Routes
   Route::controller(ActiveUserController::class)->group(function(){
    Route::get('/all/user', 'AllUser')->name('all.user');
    Route::get('/all/instructor', 'AllInstructor')->name('all.instructor');

   }) ;

   // Admin blog category ALL Routes
   Route::controller(BlogController::class)->group(function(){
    Route::get('/blog/category', 'AllBlogCategory')->name('blog.category');
    Route::post('/blog/category/store', 'BlogCategoryStore')->name('blog.category.store');
  Route::get('/edit/blog/category/{id}','EditBlogCategory'); 

  Route::post('/blog/category/update','UpdateBlogCategory')->name('blog.category.update');
  Route::get('/delete/blog/category/{id}','DeleteBlogCategory')->name('delete.blog.category');

   });
  
// Blog Post All Route 
Route::controller(BlogController::class)->group(function(){
    Route::get('/blog/post','BlogPost')->name('blog.post'); 
     Route::get('/add/blog/post','AddBlogPost')->name('add.blog.post');  
     Route::post('/store/blog/post','StoreBlogPost')->name('store.blog.post');  
     Route::get('/edit/post/{id}','EditBlogPost')->name('edit.post');  
     Route::post('/update/blog/post','UpdateBlogPost')->name('update.blog.post');  
     Route::get('/delete/post/{id}','DeleteBlogPost')->name('delete.post');  
  
    
});

// Permission  All Route 
Route::controller(RoleController::class)->group(function(){
    Route::get('/all/permission','AllPermission')->name('all.permission'); 
    Route::get('/add/permission','AddPermission')->name('add.permission'); 
    Route::post('/store/permission','StorePermission')->name('store.permission'); 
    Route::get('/edit/permission/{id}','EditPermission')->name('edit.permission'); 
    Route::post('/update/permission','UpdatePermission')->name('update.permission'); 
    Route::get('/delete/permission/{id}','DeletePermission')->name('delete.permission'); 
    Route::get('/import/permission','ImportPermission')->name('import.permission'); 
    // Excel import and export
    Route::get('/export-permissions', [ExcelController::class, 'exportPermissions'])->name('export.permissions');
Route::post('/import-permissions', [ExcelController::class, 'importPermissions'])->name('import.permissions');
});

// Roles  All Route 
Route::controller(RoleController::class)->group(function(){
    Route::get('/all/roles','AllRoles')->name('all.roles'); 
    Route::get('/add/roles','AddRoles')->name('add.roles'); 
    Route::post('/store/roles','StoreRoles')->name('store.roles'); 
    Route::get('/edit/roles/{id}','EditRoles')->name('edit.roles'); 
    Route::post('/update/roles','UpdateRoles')->name('update.roles'); 
    Route::get('/delete/roles/{id}','DeleteRoles')->name('delete.roles'); 

    Route::get('/add/roles/permission','AddRolesPermission')->name('add.roles.permission'); 
    Route::post('/role/permission/store','RolePermissionStore')->name('role.permission.store'); 
   Route::get('/all/roles/permission','AllRolesPermission')->name('all.roles.permission'); 
  Route::get('/admin/edit/roles/{id}','AdminEditRoles')->name('admin.edit.roles'); 
   Route::post('/admin/roles/update/{id}','AdminUpdateRoles')->name('admin.roles.update');
   Route::get('/admin/delete/roles/{id}','AdminDeleteRoles')->name('admin.delete.roles');

});
// Admin Management All Route 
Route::controller(AdminController::class)->group(function(){
   Route::get('/all/admin','AllAdmin')->name('all.admin'); 
    Route::get('/add/admin','AddAdmin')->name('add.admin');
    Route::post('/store/admin','StoreAdmin')->name('store.admin'); 
    Route::get('/edit/admin/{id}','EditAdmin')->name('edit.admin');
     Route::post('/update/admin/{id}','UpdateAdmin')->name('update.admin'); 
     Route::get('/delete/admin/{id}','DeleteAdmin')->name('delete.admin'); 
     
    
});
});// End Admin middleware routes

// =====================================================
// Instructor Routes (Authenticated)
// =====================================================

Route::middleware(['auth', 'role:instructor'])->group(function ()  {

    Route::controller(InstructorController::class)->group(function () {
        Route::get('/instructor/dashboard', 'instructorbord')->name('instructor.dashboard');

        Route::get('/instructor/logout', 'instructorLogout')->name('instructor.logout');
        Route::get('/instructor/profile', 'instructorProfile')->name('instructor.profile');
        Route::post('/instructor/profile/store', 'instructorProfileStore')->name('instructor.profile.store');

        Route::get('/instructor/change/password', 'instructorChangePassword')->name('instructor.change.password');
        Route::post('/instructor/change/update', 'instructorPasswordUpdate')->name('instructor.password.update');

    });

    // Instructor Course Routes
    Route::controller(CourseController::class)->group(function () {
        Route::get('/all/course', 'AllCourse')->name('all.course');
        Route::get('/add/course', 'AddCourse')->name('add.course');
        // Route::get('/subcategory/ajax/{category_id}', 'GetSubCategory')
    //  ->name('subcategory.ajax');

    Route::get('/ajax/subcategory/{category_id}', [CourseController::class, 'GetSubCategory']);
        Route::post('/store/course', 'StoreCourse')->name('store.course');

        Route::get('/edit/course/{id}', 'EditCourse')->name('edit.course');
        Route::post('/update/course', 'UpdateCourse')->name('update.course');

        Route::post('/update/course/image', 'UpdateCourseImage')->name('update.course.image');
        Route::post('/update/course/video', 'UpdateCourseVideo')->name('update.course.video');
        Route::post('/update/course/goal', 'UpdateCourseGoal')->name('update.course.goal');

        Route::get('/delete/course/{id}', 'DeleteCourse')->name('delete.course');
    });
    //   Route::get('/subcategory/ajax/{category_id}', [CourseController::class, 'GetSubCategory']);
    // Course Lecture & Section Routes
    Route::controller(CourseController::class)->group(function () {
        Route::get('/add/course/lecture/{id}', 'AddCourseLecture')->name('add.course.lecture');

        Route::post('/add/course/section', 'AddCourseSection')->name('add.course.section');
        Route::post('/save-lecture', 'SaveLecture')->name('save-lecture');

        Route::get('/edit/lecture/{id}', 'EditLecture')->name('edit.lecture');
        Route::post('/update/course/lecture', 'UpdateCourseLecture')->name('update.course.lecture');

        Route::get('/delete/lecture/{id}', 'DeleteLecture')->name('delete.lecture');
        Route::post('/delete/section/{id}', 'DeleteSection')->name('delete.section');
    });


    // Instructor Orders
    Route::controller(OrderController::class)->group(function () {
        Route::get('/instructor/all/order', 'InstructorAllOrder')->name('instructor.all.order');
        Route::get('/instructor/order/details/{payment_id}', 'InstructorOrderDetails')->name('instructor.order.details');
        Route::get('/instructor/order/invoice/{payment_id}', 'InstructorOrderInvoice')->name('instructor.order.invoice');
    });

    // Instructor Questions
    Route::controller(QuestionController::class)->group(function () {
        Route::get('/instructor/all/question', 'InstructorAllQuestion')->name('instructor.all.question');
        Route::get('/question/details/{id}', 'QuestionDetails')->name('question.details');
        Route::post('/instructor/replay', 'InstructorRepaly')->name('instructor.replay');
    });
    
    // Instructor all coupon routes 
    Route::controller(CouponController::class)->group(function () {
        Route::get('/instructor/all/coupon', 'InstructorAllCoupon')->name('instructor.all.coupon');

        Route::get('/instructor/add/coupon', 'InstructorAddCoupon')->name('instructor.add.coupon');
        Route::post('/instructor/store/coupon', 'InstructorStoreCoupon')->name('instructor.store.coupon');
        Route::get('/instructor/edit/coupon/{id}', 'InstructorEditCoupon')->name('instructor.edit.coupon');
        Route::post('/instructor/update/coupon', 'InstructorUpdateCoupon')->name('instructor.update.coupon');
        Route::get('/instructor/delete/coupon/{id}', 'InstructorDeleteCoupon')->name('instructor.delete.coupon');
       
    });
     
    // Instructor all coupon routes 
    Route::controller(ReviewController::class)->group(function () {
        Route::get('/instructor/all/review', 'InstructorAllReview')->name('instructor.all.review');

       
    });

    



});// End Instructor middleware routes

// =====================================================
// Cart Routes (Public + Auth)
// =====================================================

Route::controller(CartController::class)->group(function () {
    Route::get('/mycart', 'MyCart')->name('mycart');
    Route::get('/get-cart-course', 'GetCartCourse');
    Route::get('/cart-remove/{rowId}', 'cartRemove');
});

Route::post('/coupon_apply', [CartController::class, 'CouponApply']);
Route::post('/inscoupon_apply', [CartController::class, 'InsCouponApply']);
Route::get('/coupon-calculation', [CartController::class, 'CouponCalculation']);
Route::get('/coupon-remove', [CartController::class, 'CouponRemove']);
Route::get('/checkout', [CartController::class, 'CheckoutCreate'])->name('checkout');
Route::post('/payment', [CartController::class, 'Payment'])->name('payment');
Route::post('/stripe_order', [CartController::class, 'StripeOrder'])->name('stripe_order');

Route::post('/store/review', [ReviewController::class, 'StoreReview'])->name('store.review');
Route::get('/blog/details/{slug}', [BlogController::class, 'BlogDetails']);
Route::get('/blog/cat/list/{id}', [BlogController::class, 'BlogCatList']);
Route::get('/blog', [BlogController::class, 'BlogList'])->name('blog');

Route::post('/mark-notification-as-read/{notification}', [CartController::class, 'MarkAsRead'])->middleware('auth');

//=================================//
/// Chat post Rquest Route//

Route::post('/send-message', [ChatController::class, 'SendMessage']);

// =====================================================//
/// END Routes Accessable for all//
// =====================================================//

//52 folder




















// use App\Http\Controllers\backend\CourseController;
// use App\Http\Controllers\Backend\SettingController;
// use App\Http\Controllers\Frontend\CartController;
// use App\Http\Controllers\Frontend\IndexController;
// use App\Http\Controllers\Frontend\WishListController;
// use App\Http\Controllers\InstructorController;
// use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\AdminController;
// use App\Http\Controllers\Backend\CategoryController;
// use App\Http\Controllers\Backend\QuestionController;
// use App\Http\Controllers\CouponController;
// use App\Http\Controllers\OrderController;
// use App\Http\Controllers\UserController;
// use Illuminate\Support\Facades\Route;
// use Gloudemans\Shoppingcart\Facades\Cart;

// // Route::get('/', function () {
// //     return view('welcome');
// // });

// Route::get('/', [UserController::class, 'index'])
//     ->name('index');

// Route::get('/dashboard', function () {
//     // return view('frontend.dashboard.index');
//     return view('frontend.dashboard.index');

// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/user/profile', [UserController::class, 'UserProfile'])->name('user.profile');

//      Route::post('/user/profile/update', [UserController::class, 'UserProfileUpdate'])->name('user.profile.update');
// ///user logout
//      Route::get('/user/logout', [UserController::class, 'UserLogout'])->name('user.logout');
// //user password change
//       Route::get('/user/change/password', [UserController::class, 'UserChangePassword'])->name('user.change.password');
//     //user passeord update
//       Route::post('/user/password/update', [UserController::class, 'UserPasswordUpdate'])->name('user.password.update');
    
//     ///user wishlist all route
//     Route::controller(WishListController::class)->group(function(){

//         Route::get('/user/wishlist','AllWishist')->name('user.wishlist');
//         Route::get('/get-wishlist-course/','GetWishlistCourse');
//         Route::get('/wishlist-remove/{id}','RemoveWishlist');

//     });
//   /// User My course all route
//     Route::controller(OrderController::class)->group(function(){
//         Route::get('/my/course','MyCourse')->name('my.course');
//         Route::get('/course/view/{course_id}','CourseView')->name('course.view');
       
//     });
//  /// User Questoin all route
//     Route::controller(QuestionController::class)->group(function(){
//         Route::post('/user/question','UserQuestion')->name('user.question');
        
       
//     });


// });
// ////END Auth middleware
// require __DIR__.'/auth.php';

// //38 2

// Route::middleware(['auth'])->group(function () {

// // Admin Login Dashboard Redirect (after login) - optional route
// Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])
//     ->name('admin.admin_dashboard');

// // Admin Logout (POST)
// Route::post('/admin/logout', [AdminController::class, 'adminLogout'])->name('admin.logout');

// // Admin Profile View (GET)
// Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
   
// // Admin Profile Update (POST)
// Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])
//     ->name('admin.profile.store');
//     // ->middleware('auth');

// // Admin Change Password Page (GET)
// Route::get('/admin/change/password', [AdminController::class, 'AdminChangePassword'])
//     ->name('admin.change.password');
//     // ->middleware('auth');

// // Admin Password Update (POST)
// Route::post('/admin/password/update', [AdminController::class, 'AdminPasswordUpdate'])
//     ->name('admin.password.update');
//      // category all Route  or group category
//         Route::controller(CategoryController::class)->group(function(){
//         Route::get('/all/category','AllCategory')->name('all.category');
//         Route::get('/add/category','AddCategory')->name('add.category'); 
//         Route::post('/store/category','StoreCategory')->name('store.category');

//         Route::get('/edit/category/{id}','EditCategory')->name('edit.category');
//         Route::post('/update/category','UpdateCategory')->name('update.category');
//         Route::get('/delete/category/{id}','DeleteCategory')->name('delete.category');

//     });


//     // Sub category all Routes or group of sub category
//         Route::controller(CategoryController::class)->group(function(){
//         Route::get('/all/subcategory','AllSubCategory')->name('all.subcategory');
//         Route::get('/add/subcategory','AddSubCategory')->name('add.subcategory'); 
//         Route::post('/store/subcategory','StoreSubCategory')->name('store.subcategory');
//     Route::get('/edit/category/{id}','EditCategory')->name('edit.category');
//     Route::post('/update/category','UpdateCategory')->name('update.category');
//     Route::get('/delete/category/{id}','DeleteCategory')->name('delete.category');
//     Route::get('/edit/subcategory/{id}','EditSubCategory')->name('edit.subcategory');
//     Route::post('/update/subcategory','UpdateSubCategory')->name('update.subcategory');
//     Route::get('/delete/subcategory/{id}','DeleteSubCategory')->name('delete.subcategory');

//     });
//              // instructor All Route
//       Route::controller(AdminController::class)->group(function(){
//         Route::get('/all/instructor','AllInstructor')->name('all.instructor');

//         Route::post('/update/user/status','UpdateUserStatus')->name('update.user.status');

//     });
//        ///// Admin courses  All Route
//       Route::controller(AdminController::class)->group(function(){
//         Route::get('admin/all/course','AdminAllCourse')->name('admin.all.course');
//         Route::post('update/course/status','UpdateCourseStatus')->name('update.course.status');
//         Route::get('admin/course/details/{id}','AdminCourseDetails')->name('admin.course.details');


//     });


//     ///// Admin cuopon  All Route
//       Route::controller(CouponController::class)->group(function(){
//         Route::get('/admin/all/coupon','AdminAllCoupon')->name('admin.all.coupon');
//         Route::get('/admin/add/coupon','AdminAddCoupon')->name('admin.add.coupon');
//         Route::post('/admin/store/coupon','AdminStoreCoupon')->name('admin.store.coupon');
//         Route::get('admin/edit/coupon/{id}','AdminEditCoupon')->name('admin.edit.coupon');

//         Route::post('/admin/update/coupon','AdminUpdateCoupon')->name('admin.update.coupon');

//         Route::get('/admin/delete/coupon/{id}','AdminDeleteCoupon')->name('admin.delete.coupon');
//     });

//      ///// Admin cuopon  All Route
//       Route::controller(SettingController::class)->group(function(){
//         Route::get('/smtp/setting','SmtpSetting')->name('smtp.setting');
//         Route::post('/update/smtp','SmtpUpdate')->name('update.smtp');
     
//     });


//      ///// Admin orders  All Route
//       Route::controller(OrderController::class)->group(function(){
//         Route::get('/admin/pending/order','AdminPendingOrder')->name('admin.pending.order');
//      Route::get('/admin/order/details/{id}','AdminOrderDetails')->name('admin.order.details'); 
    
//     Route::get('/pending-confrim/{id}','PendingToConfirm')->name('pending-confrim'); 
//     Route::get('/admin/confirm/order','AdminConfirmOrder')->name('admin.confirm.order'); 
     
//     });

// });
// //End Admin middleware routs


// // Admin Login Page (GET)
// Route::get('/admin/login', [AdminController::class, 'adminLogin'])->name('admin.login');

// Route::get('/become/instructor', [AdminController::class, 'BecomeIstructor'])->name('become.instructor');


// //instructor all route
// Route::middleware(['auth'])->group(function () {

//     Route::get('/instructor/dashboard', [InstructorController::class, 'instructorbord'])
//     ->name('instructor.dashboard');

    
//     Route::get('/instructor/logout', [InstructorController::class, 'instructorLogout'])
//     ->name('instructor.logout');

//     Route::get('/instructor/profile', [InstructorController::class, 'instructorProfile'])
//     ->name('instructor.profile');

// Route::post('/instructor/profile/store', [InstructorController::class, 'instructorProfileStore'])
//     ->name('instructor.profile.store');
    
//     Route::get('/instructor/change/password', [InstructorController::class, 'instructorChangePassword'])
//     ->name('instructor.change.password');

//     Route::post('/instructor/change/update', [InstructorController::class, 'instructorPasswordUpdate'])
//     ->name('instructor.password.update');

//            // instructor All Route
//       Route::controller(CourseController::class)->group(function(){
//         Route::get('/all/course','AllCourse')->name('all.course');
//         Route::get('/add/course','AddCourse')->name('add.course');
//         Route::get('/subcategory/ajax/{category_id}','GetSubCategory');

//         Route::post('/store/course','StoreCourse')->name('store.course');

//         Route::get('/edit/course/{id}','EditCourse')->name('edit.course');
//         Route::post('update/course','UpdateCourse')->name('update.course');
        
//         Route::post('update/course/image','UpdateCourseImage')->name('update.course.image');
//         Route::post('update/course/video','UpdateCourseVideo')->name('update.course.video');
//         Route::post('update/course/goal','UpdateCourseGoal')->name('update.course.goal');
       
//         Route::get('/delete/course/{id}','DeleteCourse')->name('delete.course');


//     });


//        // Course Section and Lecture All Route
//       Route::controller(CourseController::class)->group(function(){
//         Route::get('/add/course/lecture/{id}','AddCourseLecture')->name('add.course.lecture');
//         Route::post('/add/course/section','AddCourseSection')->name('add.course.section');
//         Route::post('/save-lecture','SaveLecture')->name('save-lecture');
//         Route::get('/edit/lecture/{id}','EditLecture')->name('edit.lecture');
//         Route::post('/update/course/lecture','UpdateCourseLecture')->name('update.course.lecture');
//         Route::get('/delete/lecture/{id}','DeleteLecture')->name('delete.lecture');
//         Route::post('/delete/section/{id}','DeleteSection')->name('delete.section');
//     });
//   ///// Admin orders  All Route
//       Route::controller(OrderController::class)->group(function(){
//         Route::get('/instructor/all/order','InstructorAllOrder')->name('instructor.all.order');

//         Route::get('/instructor/order/details/{payment_id}','InstructorOrderDetails')->name('instructor.order.details');
//         Route::get('/instructor/order/invoice/{payment_id}','InstructorOrderInvoice')->name('instructor.order.invoice');
//     });

//     ///// Question  All Route
//       Route::controller(QuestionController::class)->group(function(){
//         Route::get('/instructor/all/question','InstructorAllQuestion')->name('instructor.all.question');
//         Route::get('/question/details/{id}','QuestionDetails')->name('question.details');

//     }); //End Question  All Route

// });

// // End instructor middlware 


// //routes accessable fro all
// // instructor Login Page (GET)
// Route::get('/instructor/login', [InstructorController::class, 'instructorLogin'])->name('instructor.login');

// Route::get('/course/details/{id}/{slug}',[IndexController::class,'CourseDetails']);

// Route::get('/category/{id}/{slug}',[IndexController::class,'CategoryCourse']);
// Route::get('/subcategory/{id}/{slug}',[IndexController::class,'SubCategoryCourse']);
// Route::get('/instructor/details/{id}',[IndexController::class,'InstructorDetails'])->name('instructor.details');

// Route::post('/add-to-wishlist/{course_id}',[WishListController::class,'AddToWishList']);
// Route::post('/cart/data/store/{id}',[CartController::class,'AddToCart'])->name('cart.data.store');
// Route::post('/buy/data/store/{id}',[CartController::class,'BuyToCart']);
// Route::get('/cart/data',[CartController::class,'CartData']);
// //get data form mini cart
// Route::get('/course/mini/cart',[CartController::class,'AddMiniCart']);
// Route::get('/minicart/course/remove/{rowId}',[CartController::class,'RemoveMiniCart']);

// ///Cart all route
// Route::controller(CartController::class)->group(function(){
//     Route::get('mycart','MyCart')->name('mycart');
//     Route::get('/get-cart-course','GetCartCourse');
//     Route::get('/cart-remove/{rowId}','cartRemove');
// });
// Route::post('/coupon_apply',[CartController::class,'CouponApply']);
// Route::get('/coupon-calculation',[CartController::class,'CouponCalculation']);

// Route::get('/coupon-remove',[CartController::class,'CouponRemove']);
// Route::get('/checkout',[CartController::class,'CheckoutCreate'])->name('checkout');
// Route::post('/payment',[CartController::class,'Payment'])->name('payment');

// // End routes accessable fro all
