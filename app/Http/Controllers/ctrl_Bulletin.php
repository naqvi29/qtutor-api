<?php
/*
|--------------------------------------------------------------------------
| Bulleting Controller
|--------------------------------------------------------------------------
|
| This controller will handle all Bulleting operations.
|
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use DB;

class ctrl_Bulletin extends Controller
{
    //MA - Defining Default Access Modifiers
    private   $_mdlBulletin;
    public    $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        $this->_mdlBulletin    = getModel('Bulletin');//Helper function
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

       //pass data to get categories private function
        $this->getBulletin($data,$studentId,false);

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
       $this->storeBulletin($data,$studentId);

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
    public function show(Request $request,int $studentId,int $id)
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
       $this->getBulletin($data,$studentId,$id);

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

       //pass data to get categories private function
       $this->updateBulletin($data,$studentId,$id);

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

    public function showTotal(Request $request,$studentId){
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

       //pass data to get categories private function
        $this->getCountBulletin($data,$studentId);

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->warningResponse;
    }


    private function getBulletin($request,$studentId,$id=false){

             if(isset($id) && $id!=false){
                $data  = $this->_mdlBulletin::where('StudentID','=',$studentId)->where('id','=',$id)->orderBy('id','DESC')->get();
             }
             else{
                $data  = $this->_mdlBulletin::where('StudentID','=',$studentId)->orderBy('id','DESC')->get();
            }

             if($data == null || is_null($data) || blank($data)){
                throw new Exception('No data Found', 404);
            }

            $this->successResponse['data'] =  $data;

    }

    private function storeBulletin($request,$studentId){
        $request['StudentID'] = $studentId;
        $data  = $this->_mdlBulletin::create($request);

        if($data == null || is_null($data) || blank($data)){
            throw new Exception('Something went wrong while proccesing your request', 403);
        }

        $this->successResponse['data'] =  $data;

    }

    private function updateBulletin($request,$studentId,$id){

        if(isset($request['Is_Read'])){
            $data  = $this->_mdlBulletin::where('StudentID','=',$studentId)->update($request);
            $this->getBulletin($request,$studentId);
        }else{
            $data  = $this->_mdlBulletin::where('id','=',$id)->where('StudentID','=',$studentId)->update($request);
            $this->getBulletin($request,$studentId,$id);
        }
        // $this->successResponse['data'] =  $data;


    }

    private function getCountBulletin($request,$studentId){

        $unReadCount  = $this->_mdlBulletin::where('Is_Read','=',0)->where('StudentID','=',$studentId)->get()->count();
        $readCount    = $this->_mdlBulletin::where('Is_Read','=',1)->where('StudentID','=',$studentId)->get()->count();
        $data         = array('Unread'=>$unReadCount,'Read'=>$readCount);

        if($data == null || is_null($data) || blank($data)){
           throw new Exception('No data Found', 404);
       }

       $this->successResponse['data'] =  $data;

}

}
