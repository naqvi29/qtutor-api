<?php
/*
|--------------------------------------------------------------------------
| Course Controller
|--------------------------------------------------------------------------
|
| This controller will handle all courses operations.
| GET All Course, GET Enrolled Course , GET Course by id
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use Auth;
use DB;

class ctrl_Courses extends Controller
{
    //MA - Defining Default Access Modifiers
    private   $_mdlCourse;
    private   $_mdlEnrolledCourses;
    public    $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        $this->_mdlCourse         = getModel('Courses');//Helper function
        $this->_mdlEnrolledCourses= getModel('EnrolledCourses');//Helper function
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

       //pass data to  private function
       $this->getCourses($data);

        // Return success response from public function
        if(isset($this->errorResponse['errorCode']) && $this->errorResponse['errorCode'] != 200){
            $this->response =  $this->errorResponse;
        }

        //v1.0 - If any success occur
        if(isset($this->successResponse['data'])){
            $this->response =  $this->successResponse;
        }

        //v1.0 - return final response
        return $this->response;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->warningResponse;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];

        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

       //pass data to get categories private function
       $this->storeCourses($data);

        // Return success response from public function
        if(isset($this->errorResponse['errorCode']) && $this->errorResponse['errorCode'] != 200){
            $this->response =  $this->errorResponse;
        }

        //v1.0 - If any success occur
        if(isset($this->successResponse['data'])){
            $this->response =  $this->successResponse;
        }
        return  $this->response;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {

        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        if(!is_numeric($id)){
            $this->errorResponse['errorCode'] = 422;
            $errorCode                        = 422;
            $message                          = 'Bad Request!';
            $this->errorResponse              =  array('status'=>'failed','code'=>$errorCode,'message'=>$message);
            if($this->errorReporting == true){
                $message   = 'Invalid Id! not integer';
                $this->errorResponse['error']   = array('message'=>$message);
            }
            throw new Exception($message, 422);
            // return $this->errorResponse;
        }

       //pass data to private function
       $this->getCourses($data,$id);

        // Return success response from public function
        if(isset($this->errorResponse['errorCode']) && $this->errorResponse['errorCode'] != 200){
            $this->response =  $this->errorResponse;
        }

        //v1.0 - If any success occur
        if(isset($this->successResponse['data'])){
            $this->response =  $this->successResponse;
        }
        return  $this->response;
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->warningResponse;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        if(!is_numeric($id)){
            $this->errorResponse['errorCode'] = 422;
            $errorCode                        = 422;
            $message                          = 'Bad Request!';
            $this->errorResponse              =  array('status'=>'failed','code'=>$errorCode,'message'=>$message);
            if($this->errorReporting == true){
                $message   = 'Invalid id! not integer';
                $this->errorResponse['error']   = array('message'=>$message);
            }
            throw new Exception($message, 422);
        }

       //pass data to get categories private function
       $this->updateCourses($data,$id);

        // Return success response from public function
        if(isset($this->errorResponse['errorCode']) && $this->errorResponse['errorCode'] != 200){
            $this->response =  $this->errorResponse;
        }

        //v1.0 - If any success occur
        if(isset($this->successResponse['data'])){
            $this->response =  $this->successResponse;
        }
        return  $this->response;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->warningResponse;
    }


    public function showEnrolledCourse(Request $request,int $studentId,$id=false){

        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        if(!is_numeric($studentId)){
            $this->errorResponse['errorCode'] = 422;
            $errorCode                        = 422;
            $message                          = 'Bad Request!';
            $this->errorResponse              =  array('status'=>'failed','code'=>$errorCode,'message'=>$message);
            if($this->errorReporting == true){
                $message   = 'Invalid Id! not integer';
                $this->errorResponse['error']   = array('message'=>$message);
            }
            throw new Exception($message, 422);
            // return $this->errorResponse;
        }

       //pass data to get categories private function
        $this->getEnrolledCourse($data,$studentId,$id);

        // Return success response from public function
        if(isset($this->errorResponse['errorCode']) && $this->errorResponse['errorCode'] != 200){
            $this->response =  $this->errorResponse;
        }

        //v1.0 - If any success occur
        if(isset($this->successResponse['data'])){
            $this->response =  $this->successResponse;
        }
        return  $this->response;
    }


    //MA - Get course public calls
    private function getCourses($request,$id=false){

        if(isset($request['category']) && $request['category'] !='' && $id==false){
            //MA - Get course by category id
            $data = $this->_mdlCourse::where('Category_Id','=',$request['category'])->get();
        }
        else if(isset($request['category']) && $request['category'] !='' && $id!=false){
            //MA - Get course by category id & course id
            $data = $this->_mdlCourse::where('Category_Id','=',$request['category'])->where('Course_Id','=',$id)->get();
        }
        else if((!isset($request['category']) || $request['category'] =='') && $id==false){
            //MA - Get All courses
            $data = $this->_mdlCourse::get();
        }
        else{
            //MA - Get course by course id
            $data  = $this->_mdlCourse::find($id);
        }

        if($data == null || is_null($data) || blank($data)){
            throw new Exception('No data Found', 404);
        }

        $this->successResponse['data'] =  $data;

    }

    //MA - Get course Token required calls
    private function getEnrolledCourse($request,$studentId,$id){
        $userName  = Auth::user()->username;

        if($id==false){
            //MA - Get enrolled courses by student id
            $data  = $this->_mdlEnrolledCourses::select('id','StudentId','EnrollmentNo','CourseId','CourseName','BatchId','ClassId','SubjectName','ClassStatus','Is_External_Class','TutorName','ClassTime','IsAssessment','IsActive','created_by','created_when','TutorClassLink','TutorClassRemarks','Remarks',"ClassLink")
            ->where('StudentId','=',$studentId)
            ->where('IsActive','=',1)
            ->get();
        }
        else if($id!=false){
            //MA - Get enrolled courses by student id & course id
            $data  = $this->_mdlEnrolledCourses::select('id','StudentId','EnrollmentNo','CourseId','CourseName','BatchId','ClassId','SubjectName','ClassStatus','Is_External_Class','TutorName','ClassTime','IsAssessment','IsActive','created_by','created_when','TutorClassLink','TutorClassRemarks','Remarks',"ClassLink")
            ->where('StudentId','=',$studentId)
            ->where('id','=',$id)->where('IsActive','=',1)->get();
        }
        else{
            throw new Exception('Invalid Student id', 404);
        }

        if($data == null || is_null($data) || blank($data)){
            throw new Exception('No data found', 404);
        }
        $todayDate = date('Y-m-d H:i:s');
        foreach ($data  as $key =>$values){
            if($values->ClassStatus == 'Assessment'){
                if($values->ClassTime > $todayDate){
                    $data[$key]->LaunchClass = true;
                }else{
                    $data[$key]->LaunchClass = false;
                }
            }
            if($values->ClassStatus == 'Class'){
                $data[$key]->LaunchClass = true;
            }
        }

        $this->successResponse['data'] =  $data;

    }

    private function storeCourses($request){

        $data  = $this->_mdlCourse::create($request);

        if($data == null || is_null($data) || blank($data)){
            throw new Exception('Something went wrong while proccesing your request', 403);
        }
        $this->successResponse['data'] =  $data;
    }

    private function updateCourses($request,$id){

        $data  = $this->_mdlCourse::where('Country_Id','=',$id)->update($request);
        // $this->successResponse['data'] =  $data;
        $this->getCourses($request,$id);

    }
}
