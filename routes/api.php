<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//register admin
// Route::post('/register', 'AuthController@register');
//register student
Route::post('/register-student', 'AuthController@registerStudent');
//register trainer
Route::post('/register-trainer', 'AuthController@registerTrainer');

//user auth
Route::group([
    'prefix' => 'auth'
], function () {
    //login
    Route::post('login', 'AuthController@login');
    //login with fb
    Route::post('fb-login', 'AuthController@loginWithFb');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        //get user
        Route::get('/get-user', 'AuthController@getUser');
        //logout
        Route::get('/logout', 'AuthController@logout');
    });
});


 //category routes
 Route::group(['prefix' => 'category'], function(){
    Route::get('/list','CategoryController@index');
    Route::post('/save','CategoryController@store');
    Route::get('/show/{id}','CategoryController@show');
    Route::get('/edit/{id}','CategoryController@edit');
    Route::post('/update/{id}','CategoryController@update');
    Route::get('/delete/{id}','CategoryController@destroy');
    Route::get('/toggle-status/{id}','CategoryController@toggleStatus');
    Route::post('/filter','CategoryController@filter');
});


/// student routes
Route::group(['prefix' => 'student'], function () {

    //offers
    Route::group(['prefix' => 'offers'], function () {
        Route::get('/get-promotions','SpecialOfferController@getAllPromotionList');
        Route::get('/show/{id}','SpecialOfferController@show');
    });

    //category
    Route::group(['prefix' => 'category'], function () {
        Route::get('/all-categories', 'CategoryController@getAllStudentCategory');
    });

    //courses
    Route::group(['prefix' => 'courses'], function () {
        Route::get('/list','CourseController@index');
        Route::post('/get-by-location', 'CourseController@getCourseByLocation');
        Route::get('/all-courses/category/{id}', 'CourseController@getAllCoursesByCategory');
    });

    //student protected routes
    Route::group([
        'middleware' => ['auth:api','student:api']
    ], function() {

        //make booking
        Route::group(['prefix' => 'booking'], function () {
            Route::post('/make-course-booking', 'CourseBookingController@makeCourseBooking');
            Route::get('/my-booking', 'CourseBookingController@myBooking');
            Route::post('/book-on-paypal-success', 'CourseBookingController@bookAfterPaypalSuccess');

            //Route::post('/make-course-booking', 'PaymentController@request');

            Route::get('/my-booking/{id}', 'CourseBookingController@myBookingById');
            Route::post('/make-course-booking-by-offer/{id}', 'CourseBookingController@makeCourseBookingByOffer');
            Route::post('/book-offer-on-paypal-success/{id}', 'CourseBookingController@bookOfferAfterPaypalSuccess');

        });

        //courses
        Route::group(['prefix' => 'courses'], function () {
            Route::get('/all-courses', 'CourseController@getAllCoursesList');
        });

        // reviews
        Route::group(['prefix' => 'rating'], function(){
            //Route::post('/save','CourseReviewController@store');
            Route::get('/get','CourseReviewController@getReviewByUserId');                                
        });

        //transactions
        Route::group(['prefix' => 'transaction'], function () {
            Route::get('/my-transactions', 'BookingPaymentController@myBookingPayment');
            Route::get('/my-transactions/{id}', 'BookingPaymentController@myBookingPaymentByBookingId');
        });

        //test
        Route::group(['prefix' => 'tests'], function () {
            Route::get('/course/{id}', 'CourseTestController@getAllTestByCourse');
            Route::get('/show/{id}', 'CourseTestController@show');
            Route::get('/all-questions/{test_id}','TestQuestionController@getQuestionsByTest');
            Route::get('/question/show/{id}','TestQuestionController@show');
        });

        //profile
        Route::group(['prefix' => 'profile'], function () {
            Route::get('/get', 'StudentController@getProfile');
            Route::post('/update-password', 'StudentController@updatePass');
            Route::post('/update-profile', 'StudentController@updateProfile');
            Route::post('/update-studentcourse/{id}', 'StudentController@updateStudentCourse');
            Route::get('/trainer-list', 'CourseBookingController@getTrainerList');
        });

        //notification
        Route::group(['prefix' => 'notification'], function () {
            Route::get('/get-notification', 'NotificationController@notifyStudent');
        });

        //test result
        Route::group(['prefix' => 'test/result'], function () {
            Route::post('/add-answers', 'TestResultController@addAnswer');
            Route::get('/test-result/{test_result_id}', 'TestResultController@getTestResult');
            Route::get('/all/{type}', 'TestResultController@getMyTestResults');
            Route::get('/{id}', 'TestResultController@getMyTestResultById');
        });

        //message
        Route::group(['prefix' => 'message'], function () {
            Route::post('/send-message', 'MessageController@sendMessage');
            Route::get('/get-chat/{trainer_id}', 'MessageController@getChat');
        });

    });

});



// trainer routes
Route::group(['prefix' => 'trainer'], function () {

    //trainer protected routes
    Route::group([
        'middleware' => ['auth:api','trainer:api']
    ], function() {
        //profile
        Route::group(['prefix' => 'profile'], function () {
            Route::get('/get', 'TrainerController@getProfile');
            Route::post('/update-password', 'TrainerController@updatePass');
            Route::post('/update-profile', 'TrainerController@updateProfile');
            Route::get('/student-list', 'CourseBookingController@getStudentList');
        });

        //course
        Route::group(['prefix' => 'course'], function () {
            Route::post('/add-course', 'CourseController@addCourse');
            Route::post('/update-course/{id}', 'CourseController@updateCourseByTrainer');
            Route::get('/my-courses', 'CourseController@myCourse');            
        });

        // reviews
        Route::group(['prefix' => 'rating'], function(){
            //Route::post('/save','CourseReviewController@store');
            Route::get('/get','CourseReviewController@getReviewByTrainerId');                                
        });

        //course booking
        Route::group(['prefix' => 'booking'], function () {
            Route::get('/view-students-booking', 'CourseBookingController@viewStudentBooking');
            Route::get('/my-booking/{id}', 'CourseBookingController@myBookingById');
            Route::post('/change-booking-status/{id}', 'CourseBookingController@changeBookingStatus');
            Route::post('/schedule/{id}', 'CourseBookingController@scheduleBooking');
        });

        //transactions
        Route::group(['prefix' => 'transaction'], function () {
            Route::get('/my-transactions', 'BookingPaymentController@myStudentBookingPayment');
            Route::get('/my-transactions/{id}', 'BookingPaymentController@myBookingPaymentByBookingId');
        });

         //notification
         Route::group(['prefix' => 'notification'], function () {
            Route::get('/get-notification', 'NotificationController@notifyTrainer');
        });

        //message
        Route::group(['prefix' => 'message'], function () {
            Route::post('/send-message', 'MessageController@sendMessageByTrainer');
            Route::get('/get-chat/{student_id}', 'MessageController@getChatByTrainer');
        });

        //offers
        Route::group(['prefix' => 'offers'], function () {
            Route::get('/my-promotions','SpecialOfferController@getTrainerPromotionList');
            Route::post('/add-promotion','SpecialOfferController@addPromotion');
            Route::get('/show/{id}','SpecialOfferController@show');
        });

        //categoryrequest
        Route::group(['prefix' => 'categoryrequest'], function () {
            Route::post('/make-request', 'CategoryController@makeCategoryRequest');
        });

    });
});



// admin routes
Route::group(['prefix' => 'admin'], function () {

    //admin protected routes
    Route::group([
        'middleware' => ['auth:api','admin:api']
    ], function() {

        //dashboard
        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/get','AdminController@dashboard');
            Route::get('/get-event-calender','EventCalenderController@getAllEvents');
            Route::get('/get-event-calender-detail/{id}','EventCalenderController@getEventDetail');
            Route::post('/add-remark','AdminController@addRemark');
        });

        //document
        Route::group(['prefix' => 'document'], function () {
            Route::post('/save','AdminController@saveDocument');
            Route::get('/get','AdminController@getAllDocument');
        });

        //admin user routes
        Route::group(['prefix' => 'user'], function(){
            Route::post('/create-user','AdminController@createAdminUser');
            Route::get('/show/{id}','AdminController@findUser');
            Route::get('/toggle-status/{id}','AdminController@toggleStatus');
            Route::get('/list','AdminController@listUser');
            Route::get('/delete-user/{id}','AdminController@deleteUser');
            Route::post('/update-user/{id}','AdminController@updateUser');
            Route::post('/update-password/{id}','AdminController@updateUserPassword');
            Route::post('/filter','AdminController@filter');
        });

        Route::group(['prefix' => 'student'], function(){
            Route::get('/list','StudentController@getStudentList');
            Route::get('/toggle-status/{id}','AdminController@toggleStatus');
            Route::post('/create-student', 'AuthController@registerStudent');
            Route::post('/update-password/{id}','AdminController@updateUserPassword');
            Route::get('/show/{id}','StudentController@findStudentById');
            Route::post('/update-student/{id}', 'StudentController@updateStudentProfileByAdmin');
            Route::post('/filter','StudentController@filter');
        });

        Route::group(['prefix' => 'trainer'], function(){
            Route::get('/list','TrainerController@getTrainerList');
            Route::get('/toggle-status/{id}','AdminController@toggleStatus');
            Route::post('/create-trainer', 'AuthController@registerTrainer');
            Route::post('/update-password/{id}','AdminController@updateUserPassword');
            Route::get('/show/{id}','TrainerController@findTrainerById');
            Route::post('/update-trainer/{id}', 'TrainerController@updateTrainerProfile');
            Route::post('/filter','TrainerController@filter');
        });

        //course booking
        Route::group(['prefix' => 'coursebooking'], function () {
            Route::get('/get', 'CourseBookingController@getAllBookingByAdmin');
            Route::post('/filter', 'CourseBookingController@filterByAdmin');
        });

        //transactions history
        Route::group(['prefix' => 'transactions'], function () {
            Route::get('/get', 'BookingPaymentController@getTransactionsListByAdmin');
            Route::post('/change-status/{id}', 'BookingPaymentController@changeTransactionStatus');
            Route::post('/filter', 'BookingPaymentController@filterByAdmin');
        });

        //test result
        Route::group(['prefix' => 'testresult'], function () {
            Route::get('/get', 'TestResultController@getStudentTestResult');
            Route::post('/filter', 'TestResultController@getFilterStudentTestResult');
        });

        //notification
         Route::group(['prefix' => 'notification'], function () {
            Route::get('/get-notification', 'NotificationController@notifyAdmin');
            Route::post('/filter', 'NotificationController@filter');
        });

        //categoryrequest
        Route::group(['prefix' => 'categoryrequest'], function () {
            Route::get('/get-list', 'CategoryController@getCategoryRequestList');
            Route::post('/change-status/{id}', 'CategoryController@changeStatusCategoryRequest');
        });

        //promotions
        Route::group(['prefix' => 'promotions'], function () {
            Route::post('/save', 'AdminPromotionController@savePromotion');
            Route::get('/show/{id}', 'AdminPromotionController@show');
            Route::get('/list', 'AdminPromotionController@list');
            Route::get('/delete/{id}', 'AdminPromotionController@delete');
            Route::post('/update/{id}', 'AdminPromotionController@update');
            Route::get('/toggle-status/{id}','AdminPromotionController@toggleStatus');
            Route::get('/send/{id}', 'AdminPromotionController@sendPromotion');
            Route::post('/filter', 'AdminPromotionController@filter');

        });

    });
});
//end admin routes
        

//notification detail
Route::group(['prefix' => 'notification'], function () {
    Route::get('/detail/{id}', 'NotificationController@getNotificationDetail');
});

 //user routes
Route::group(['prefix' => 'course'], function(){
    Route::get('/list','CourseController@index');
    Route::post('/save','CourseController@store');
    Route::post('/update/{id}','CourseController@update');
    Route::get('/show/{id}','CourseController@show');
    Route::get('/edit/{id}','CourseController@edit');
    Route::get('/delete/{id}','CourseController@destroy');
    Route::get('/location/{loc_id}','CourseController@removeCourseLocation');
    Route::get('/toggle-status/{id}','CourseController@toggleStatus');
    Route::post('/gallery','CourseController@updateCourseImages');
    //Route::post('/gallery/image','CourseController@saveCourseImages');
    Route::get('/image/delete/{id}','CourseController@deleteCourseImage');
    Route::get('/location/delete/{id}','CourseController@deleteCourseLocation');
    Route::post('/filter','CourseController@filter');


    // reviews
    Route::group(['prefix' => 'reviews'], function(){
        Route::get('/{course_id}','CourseReviewController@index');
        Route::post('/save','CourseReviewController@store');
        Route::post('/reply','CourseReviewController@reviewReply');
        Route::post('/update/{id}','CourseReviewController@update');
        Route::get('/delete/{id}','CourseReviewController@destroy');
        Route::post('/filter','CourseReviewController@filterByAdmin');
    });


    // tests
    Route::group(['prefix' => 'test'], function(){
        Route::get('/{course_id}','CourseTestController@index');
        Route::post('/save','CourseTestController@store');
        Route::get('/show/{id}','CourseTestController@show');
        Route::post('/update/{id}','CourseTestController@update');
        Route::get('/delete/{id}','CourseTestController@destroy');
        Route::get('/toggle-status/{id}','CourseTestController@toggleStatus');
        Route::post('/filter','CourseTestController@filterByAdmin');
    });

    // Route::post('/update/gallery','CourseController@updateCourseGallary');
});


    // reviews
    Route::group(['prefix' => 'test/question'], function(){
        Route::get('/list/{course_test_id}','TestQuestionController@index');
        Route::get('/show/{course_test_id}','TestQuestionController@show');
        Route::post('/save','TestQuestionController@store');
        Route::get('/toggle-status/{id}','TestQuestionController@toggleStatus');
        Route::post('/update/{id}','TestQuestionController@update');
        Route::get('/delete/{id}','TestQuestionController@destroy');
        Route::post('/filter','TestQuestionController@filterByAdmin');
    });


    //user routes
    // Route::group(['prefix' => 'user'], function(){
    //     Route::get('/list','AuthController@index');
    //     Route::get('/show/{id}','AuthController@show');
    //     Route::get('/edit/{id}','AuthController@edit');
    //     Route::post('/update/basic/{id}','AuthController@update');
    //     Route::post('/update/password/{id}','AuthController@updatePassword');
    // });

    // / contact us routs
    Route::post("/contact-us/save","FrontController@saveContactUs");
    Route::post("/contact-us/filter","FrontController@filterContact");
    Route::get("/contact-us/list","FrontController@getContactUsList");
    Route::get("/contact-us/show/{id}","FrontController@getContactUsById");
    Route::get("/contact-us/delete/{id}","FrontController@deleteContactUsById");

    Route::post("/contact-detail/save","FrontController@saveContactDetail");
    Route::get("/contact-detail/get","FrontController@getContactDetailData");



// Route::group(['middleware' => 'auth:api'], function()
// {


    Route::group(['prefix' => 'special-offer'], function () {
        Route::get('/list','SpecialOfferController@index');
        Route::post('/save','SpecialOfferController@store');
        Route::get('/show/{id}','SpecialOfferController@show');
        Route::get('/toggle-status/{id}','SpecialOfferController@toggleStatus');
        Route::post('/update/{id}','SpecialOfferController@update');
        Route::get('/delete/{id}','SpecialOfferController@destroy');
        Route::post('/filter','SpecialOfferController@filter');
    });

    Route::group(['prefix' => 'front'], function () {

        Route::group(['prefix' => 'footer'], function () {
            Route::get('/get','FrontController@getFooterData');
            Route::post('/save','FrontController@saveFooterData');
        });

        Route::group(['prefix' => 'gallery'], function () {
            Route::get('/get','FrontController@getGallData');
            Route::post('/add','FrontController@saveGallData');
        });

        // home section

        Route::group(['prefix' => 'home'], function () {
            Route::get('/get','FrontController@getHomeData');
            Route::post('/save-institute/section','FrontController@saveHomeFeatureInstitueSection');
            Route::post('/save-slider/section','FrontController@saveHomeSliderSection');
            // Route::post('/save-slider/section','FrontController@saveHomeSliderSection');
        });


        Route::group(['prefix' => 'blog'], function () {
            Route::post('/save','BlogController@store');
            Route::post('/filter','BlogController@filter');
            Route::post('/update/{id}','BlogController@update');
            Route::get('/list','BlogController@index');
            Route::get('/show/{id}','BlogController@show');
            Route::get('/delete/{id}','BlogController@destroy');

        });

        Route::group(['prefix' => 'header'], function () {
            Route::get('/setting','FrontController@getHeaderData');
            Route::post('/setting/save','FrontController@saveHeaderData');
        });

        Route::group(['prefix' => 'about'], function () {
            Route::get('/setting','FrontController@getAboutData');
            Route::post('/setting/save','FrontController@saveAboutData');
        });

        Route::group(['prefix' => 'copyright'], function () {
            Route::get('/get','FrontController@getCopyRightData');
            Route::post('/save','FrontController@saveCopyRightData');
        });

        Route::group(['prefix' => 'insta-gallery'], function () {
            Route::get('/get','FrontController@getInstaData');
            Route::post('/save','FrontController@saveInstaData');
        });

        Route::group(['prefix' => 'home-slider'], function () {
            Route::get('/get','FrontController@getSliderData');
            Route::post('/save','FrontController@saveSliderData');
        });

        Route::get('/site/settings/get','SettingController@getSiteSetting');
        Route::post('/site/settings/save','SettingController@upadteSiteSetting');

    });
    

// });



