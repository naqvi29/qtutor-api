<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use DB;

class ctrl_Books extends Controller
{
    private   $_mdlBooks;
    private   $_mdlCompany;
    public    $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        $this->_mdlBooks   = getModel('Books');//Helper function
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,int $studentId)
    {
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
                $message   = 'Invalid Student id! not integer';
                $this->errorResponse['error']   = array('message'=>$message);
            }
            throw new Exception($message, 422);
            // return $this->errorResponse;
        }

       //pass data to  private function
       $this->getBooks($data,$studentId);

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
        return $this->warningResponse;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$studentId,$id)
    {

        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        if(!is_numeric($id)){
            $this->errorResponse['errorCode'] = 403;
            $errorCode                        = 403;
            $message                          = 'Bad Request!';
            $this->errorResponse              =  array('status'=>'failed','code'=>$errorCode,'message'=>$message);
            if($this->errorReporting == true){
                $message   = 'Invalid Id! not integer';
                $this->errorResponse['error']   = array('message'=>$message);
            }
            return $this->errorResponse;
        }

       //pass data to private function
       $this->getBooks($data,$studentId,$id);

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
        return $this->warningResponse;
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



    private function getBooks($request,$studentId,$id=false){

            $bookDownloadUrl = getCompanyInfo()->find(1)->value('Book_Download_URL');

            if($id==false){
                $data  = $this->_mdlBooks::select('id','book_id','book_name','student_id',DB::raw('CONCAT("'.$bookDownloadUrl.'","?id=",id,"&update=1") AS DownloadUrl'))->where('student_id','=',$studentId)->where('id_downloaded',0)->orderBy('id','DESC')->get();
            }
            else{
                $data  = $this->_mdlBooks::select('id','book_id','book_name','student_id',DB::raw('CONCAT("'.$bookDownloadUrl.'","?id=",id,"&update=1") AS DownloadUrl'))->where('student_id','=',$studentId)->where('id_downloaded',0)->where('id','=',$id)->get();
            }

            if($data == null || is_null($data) || blank($data)){
                throw new Exception('No data Found', 404);
            }

            $this->successResponse['data'] =  $data;

    }


    private function getEnrolledCourse($request,$studentId,$courseId){

            if($courseId==false){
                $data  = $this->_mdlEnrolledCourses::where('StudentId','=',$studentId)->get();
            }
            else if($courseId!=false){
                $data  = $this->_mdlEnrolledCourses::where('StudentId','=',$studentId)->where('CourseId','=',$courseId)->get();
            }
            else{
                throw new Exception('Invalid Student id', 404);
            }

            if($data == null || sizeof($data) <=0){
                throw new Exception('No data found', 404);
            }

            $this->successResponse['data'] =  $data;


    }

}

