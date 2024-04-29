<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use DB;

class ctrl_Curriculum extends Controller
{
    //Variable init
    private   $_mdlCurriculum;
    private   $_mdlCurriculum_Info;
    public    $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];


    //Load model on class load
    public function __construct(){
        $this->_mdlCurriculum=getModel('Curriculum');
        $this->_mdlCurriculum_Info=getModel('Curriculum_Info');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // Get Request: Category list
    public function index(Request $request,$courseId)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        if(!is_numeric($courseId)){
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

        //Sending data to private function
        $this->getCurriculum($data,$courseId);

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
    public function show(Request $request,$courseId,$id)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        if(!is_numeric($id) || !is_numeric($courseId)){
            $this->errorResponse['errorCode'] = 403;
            $errorCode                        = 403;
            $message                          = 'Bad Request!';
            $this->errorResponse              =  array('status'=>'failed','code'=>$errorCode,'message'=>$message);
            if($this->errorReporting == true){
                $message   = 'Invalid Id! not integer';
                $this->errorResponse['error']   = array('message'=>$message);
            }
            throw new Exception("Invalid Id: No Data found", 404);
            // return $this->errorResponse;
        }

       //pass data to get categories private function
       $this->getCurriculum($data,$courseId,$id);

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


    private function getCurriculum($request,$courseId,$id=false){

            if(isset($id) && $id!=false){
                //  $data = $this->_mdlCurriculum::where('Course_Id','=',$courseId)->where('Curriculum_Id','=',$id)->first();
                $data         = $this->_mdlCurriculum_Info::where('Curriculum_Id','=',$id)->get();
            }
            else{
                $curriculumId = $this->_mdlCurriculum::where('Course_Id','=',$courseId)->pluck('Curriculum_Id')->toArray();
                if(blank($curriculumId)){
                    throw new Exception('No Curriculum Found', 404);
                }
                $data         = $this->_mdlCurriculum_Info::where('Curriculum_Id','=',$curriculumId[0])->get();
                // $data  = $this->_mdlCurriculum::select('Curriculum_Id','Curriculum_Name')->with('curriculumInfo')->where('Course_Id', '=', $courseId)->get();
            }
            if($data == null || is_null($data) || blank($data)){
                throw new Exception('No data Found', 404);
            }
            $this->successResponse['data'] =  $data;

    }


}
