<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use DB;

class ctrl_Support extends Controller
{
    // private   $_mdlEnrollmentRequest;
    public    $response;
    private   $_mdlCountry;
    public    $SupportEmail    = 'support@qtvtutor.com';
    public    $SupportToName   = "QTVTutorSupport";
    public    $MailSubject     = "QTutor Support Request";
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        // $this->_mdlEnrollmentRequest = getModel('Support');//Helper function
        $this->_mdlCountry=getModel('Country');

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
       $this->supportEmail($data);

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
    public function show(Request $request,$id)
    {
        return $this->warningResponse;
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


    private function supportEmail($request){
            $email                  = (isset($request['Email']) && $request['Email'] !='' ? $request['Email'] : false);
            $name                   = (isset($request['Name']) && $request['Name'] !='' ? $request['Name'] : 'Student Support Ticket');
            $message                = (isset($request['Message']) && $request['Message'] !='' ? $request['Message'] : false);
            $Country                = (isset($request['Country']) && $request['Country'] !='' ? $request['Country'] : '');
            if($Country == ''){
                $Country = getCompanyInfo()->find(1)->value('Default_Country_Id');
            }

            $countryInfo            = $this->_mdlCountry::select('Country_Name')->find($Country);
            $request['Country']     = $countryInfo['Country_Name'];
            $this->SupportEmail     = getCompanyInfo()->find(1)->value('Company_Support_Email');


            $subject                = $this->MailSubject;
            $body                   = array('data'=>$request);
            $mailTemplate           = 'support_guest';
            $fromName               = $name;
            if(!$email ||!$message||!$name){
                throw new Exception("No Email found", 401);
            }
            sendEmail($this->SupportEmail,$this->SupportToName,$email,$fromName,$subject,$body,$mailTemplate);

            //$response = $this->_mdlEnrollmentRequest->create($request);
            $this->successResponse['data'] =  $this->successResponse;

    }


}
