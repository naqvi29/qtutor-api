<?php
/*
|--------------------------------------------------------------------------
| Recording Controller
|--------------------------------------------------------------------------
|
| This controller will handle all Recording operations.
|
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use DB;

class ctrl_Recording extends Controller
{
    //MA - Defining Default Access Modifiers
    private   $_mdlRecording;
    private   $_mdlClassRecording;
    public    $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        $this->_mdlRecording      = getModel('Recording');//Helper function
        $this->_mdlClassRecording = getModel('ClassRecording');//Helper function
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$studentId)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        if(!isset($studentId) || !is_numeric($studentId)){
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
       $this->getRecording($data,$studentId);

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
    public function store(Request $request,$studentId)
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

       //pass data to get categories private function
       $this->storeRecording($data,$studentId);

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

       //pass data to get categories private function
       $this->getRecording($data,$studentId,$id);

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
    public function update(Request $request,$studentId, $id)
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
                $message   = 'Invalid Student id! not integer';
                $this->errorResponse['error']   = array('message'=>$message);
            }
            throw new Exception($message, 422);
            // return $this->errorResponse;
        }

       //pass data to get categories private function
       $this->updateRecording($data,$studentId,$id);

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


    //MA - Get recording function
    private function getRecording($request,$studentId,$id=false){
        $isStudentAllowed = $this->_mdlClassRecording::where('IsAllowed',1)->where('StudentId',$studentId)->first();
        $data = [];
        if(!blank($isStudentAllowed) && $isStudentAllowed->StudentId == $studentId){
            if($id==false){
                //Get recording by id - when using concat or special cases in mysql, we will be using db row query to
                $data  = $this->_mdlRecording::select('id','Recording_Url','Student_ID','Message_ID','Duration',DB::raw("DATE_FORMAT(DATE(Start_Date),'%m/%d/%Y') AS Recording_Name"),DB::raw("RIGHT(Recording_URL,4) as Recording_Format"))->where('Student_ID','=',$studentId)->orderBy('id','DESC')->get();
            }else{
                //Get all recording - when using concat or special cases in mysql, we will be using db row query to
                $data  = $this->_mdlRecording::select('id','Recording_Url','Student_ID','Message_ID','Duration',DB::raw("DATE_FORMAT(DATE(Start_Date),'%m/%d/%Y')  AS Recording_Name"),DB::raw("RIGHT(Recording_URL,4) as Recording_Format"))->where('id','=',$id)->where('Student_ID','=',$studentId)->get();
            }
        }
            if($data == null || is_null($data) || blank($data)){
                throw new Exception('No data found', 404);
            }

            $this->successResponse['data'] =  $data;

    }

    private function storeRecording($request,$studentId){
        $request['Student_ID'] = $studentId;
        $data  = $this->_mdlRecording::create($request);

        if($data == null || is_null($data) || blank($data)){
            throw new Exception('Something went wrong while proccesing your request', 403);
        }

        $this->successResponse['data'] =  $data;

    }
}
