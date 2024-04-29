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

// v1.0 - Middleware for client and header authentication
Route::middleware('header.auth')->group(function(){

    // v1.0 - Authentication (Auth prefix Group)
    Route::prefix('/auth')->group(function(){
        Route::post('/student/login','App\Http\Controllers\ctrl_User@login');
        Route::post('/parent/login','App\Http\Controllers\ctrl_User@loginParent');
        Route::post('/user/logout','App\Http\Controllers\ctrl_User@logout')->middleware('auth:api');
    });

    //v1.0 After login calls ( Required Access Token )
    Route::middleware('auth:api')->group(function(){

        //v1.0 -Student Section ( Required Access Token )
        Route::prefix('/students/{studentId}/')->group(function(){

            Route::get('courses/{courseId?}','App\Http\Controllers\ctrl_Courses@showEnrolledCourse');
            Route::get('recordings','App\Http\Controllers\ctrl_Recording@index');
            Route::post('recordings','App\Http\Controllers\ctrl_Recording@store');
            Route::get('recordings/{recordingId}','App\Http\Controllers\ctrl_Recording@show');
            Route::get('bulletins','App\Http\Controllers\ctrl_Bulletin@index');
            Route::get('bulletins/count','App\Http\Controllers\ctrl_Bulletin@showTotal');
            Route::post('bulletins','App\Http\Controllers\ctrl_Bulletin@store');
            Route::put('bulletins/{id}','App\Http\Controllers\ctrl_Bulletin@update');
            Route::get('bulletins/{id}','App\Http\Controllers\ctrl_Bulletin@show');
            Route::resource('books','App\Http\Controllers\ctrl_Books');
            Route::get('books/{bookId}','App\Http\Controllers\ctrl_Books@show');
            Route::get('class/attendance','App\Http\Controllers\ctrl_Attandance@index');
            Route::get('class/attendance/{id}','App\Http\Controllers\ctrl_Attandance@show');
            Route::post('class/attendance/{id}/message','App\Http\Controllers\ctrl_Attandance@storeMessage');
            Route::get('class/attendance/{id}/message','App\Http\Controllers\ctrl_Attandance@showAllMessages');
            Route::get('class/attendance/{id}/message/{messageId}','App\Http\Controllers\ctrl_Attandance@showSelectedMessages');
            Route::put('class/attendance/{id}/message/{messageId}','App\Http\Controllers\ctrl_Attandance@editMessage');
            Route::resource('support/ticket','App\Http\Controllers\ctrl_SupportTicket');
            Route::get('invoice','App\Http\Controllers\ctrl_Invoice@index');
            Route::get('invoice/{id}','App\Http\Controllers\ctrl_Invoice@show');
            Route::get('/','App\Http\Controllers\ctrl_Students@show');

        });

        //v1.0 -Parent Section ( Required Access Token )
        Route::prefix('/parent')->group(function(){
            Route::get('students','App\Http\Controllers\ctrl_Students@index');
            Route::get('students/{studentId}','App\Http\Controllers\ctrl_Students@show');

        });
    });

    // v1.0 Public calls
    Route::prefix('/')->group(function(){
        Route::resource('countries','App\Http\Controllers\ctrl_Country');
        Route::get('company/{companyId}','App\Http\Controllers\ctrl_Company@show');
        Route::resource('courses/categories','App\Http\Controllers\ctrl_Categories');
        Route::resource('courses','App\Http\Controllers\ctrl_Courses');
        Route::get('courses/{courseId?}/countries/{countryId?}/plans/{planId?}','App\Http\Controllers\ctrl_PlansMeta@show');
        Route::get('courses/{courseId}/curriculums/','App\Http\Controllers\ctrl_Curriculum@index');
        Route::get('courses/{courseId}/curriculums/{curriculumId}','App\Http\Controllers\ctrl_Curriculum@show');
        Route::resource('enrollment/request','App\Http\Controllers\ctrl_EnrollmentRequest');
        Route::post('support/email','App\Http\Controllers\ctrl_Support@index');
        Route::post('notifications/tokens','App\Http\Controllers\ctrl_Notification@createToken');
        Route::post('notifications/all','App\Http\Controllers\ctrl_Notification@sendNotificationToAll');
        Route::post('class/attendance/{id}/message','App\Http\Controllers\ctrl_Attandance@storeMessageAdmin');
        Route::get('class/attendance/{id}/message','App\Http\Controllers\ctrl_Attandance@showMessageAllAdmin');
        Route::get('class/attendance/{id}/student/{studentId}/message','App\Http\Controllers\ctrl_Attandance@showMessageStudentAdmin');
        // Route::post('notifications','App\Http\Controllers\ctrl_Notification@sendNotificationByToken');
        Route::post('notifications/students','App\Http\Controllers\ctrl_Notification@sendNotificationToStudents');
        Route::post('notifications/students/{id}','App\Http\Controllers\ctrl_Notification@sendNotificationToStudents');
        Route::post('notifications/tutor/{tutorId}','App\Http\Controllers\ctrl_Notification@sendNotificationTutorToStudent');
        Route::post('notifications/tutor/{tutorId}/course/{courseId}','App\Http\Controllers\ctrl_Notification@sendNotificationTutorCourseToStudent');
        Route::post('notifications/course/{courseId}','App\Http\Controllers\ctrl_Notification@sendNotificationCourseToStudent');
        Route::post('notifications/guests','App\Http\Controllers\ctrl_Notification@sendNotificationToGuest');
        Route::post('notifications/country/{countryId}','App\Http\Controllers\ctrl_Notification@sendNotificationToCountry');
    });

});

    // v1.0 Zoho
    Route::prefix('/zoho')->group(function(){
        Route::get('auth','App\Http\Controllers\ctrl_ZohoAuth@index');
        Route::get('code','App\Http\Controllers\ctrl_ZohoAuth@store');
    });



// v1.0 - Unauthorized token (If token not authenticated or empty)
Route::get('unauthorization',function(){
    return response()->json(array('status'=>'failed','code'=>'401','message'=>'This user authentication token has been removed or expired'));
});

// v1.0 - If unauthorized route or method (If route/api url is incorrect and wrong method name)
Route::any('{any}', function(){
    return response()->json([
        'status'    => 'failed',
        'code'      => 404,
        'message'   => 'You have an invalid URL or METHOD set in the URL Path property of a method from a REST API',
    ], 404);
})->where('any', '.*');

