<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use DB;

class ctrl_Country extends Controller
{
    //Variable init
    private   $_mdlCountry;
    public    $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];


    //Load model on class load
    public function __construct(){
        $this->_mdlCountry=getModel('Country');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // Get Request: Countries list
    public function index(Request $request)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        $queryParam     = checkForParam($request->query());
        //Sending data to private function
        $this->getCountries($data,false,$queryParam);

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
       $this->storeCountry($data);

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
    public function show(Request $request,$id=null)
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
       $this->getCountries($data,$id,false);

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
       $this->updateCountries($data,$id);

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


    private function getCountries($request,$id=false,$queryParam){
        $baseFlagPath =  url('storage/assets/images/flags/png/');

        if($id!=false){
            // $data = $this->_mdlCountry::find($id);
            $data  = $this->_mdlCountry::select('Country_Id','Country_ISO_Code','Country_Name','Country_Title','Country_Currency',DB::raw("CONCAT('$baseFlagPath','/',LOWER(Country_ISO_Code),'.png') AS Country_Icon"))->where('Country_Id','=',$id)->get();
        }
        else{
            if($queryParam == false){
                $data  = $this->_mdlCountry::select('Country_Id','Country_ISO_Code','Country_Name','Country_Title','Country_Currency',DB::raw("CONCAT('$baseFlagPath','/',LOWER(Country_ISO_Code),'.png') AS Country_Icon"))->get();
            }else{
                $data  = $this->_mdlCountry::select('Country_Id','Country_ISO_Code','Country_Name','Country_Title','Country_Currency',DB::raw("CONCAT('$baseFlagPath','/',LOWER(Country_ISO_Code),'.png') AS Country_Icon"))->where('Country_Name','LIKE','%'.$queryParam['Country_Name'].'%')->get();
            }
            //  $data = $this->_mdlCountry::get();

        }

        if($data == null || is_null($data) || blank($data)){
            throw new Exception('No data Found', 404);
        }
        $this->successResponse['data'] =  $data;

    }

    private function storeCountry($request){

        $data  = $this->_mdlCountry::create($request);

        if($data == null || is_null($data) || blank($data)){
            throw new Exception('Something went wrong while proccesing your request', 403);
        }

        $this->successResponse['data'] =  $data;

    }

    private function updateCountries($request,$id){

        $data  = $this->_mdlBulletin::where('Country_Id','=',$id)->update($request);
        // $this->successResponse['data'] =  $data;
        $this->getCountries($request,$id);

    }
}
