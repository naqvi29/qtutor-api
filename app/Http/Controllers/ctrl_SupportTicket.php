<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use Mail;

class ctrl_SupportTicket extends Controller
{
    //MA - Defining Default Access Modifiers
    private   $_mdlEnrollmentRequest;
    private   $_mdlCountry;
    public    $response;
    public    $SupportEmail    = 'support@qtvtutor.com';
    public    $SupportToName   = "QTVTutorSupport";
    public    $MailSubject     = "QTutor Support Request";
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        $this->_mdlEnrollmentRequest = getModel('EnrollmentRequest');//Helper function
        $this->_mdlCountry=getModel('Country');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->warningResponse;
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

        //pass data to  private function
       $this->supportTicket($data,$studentId);

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


    //MA - Get course Token required calls
    private function supportTicket($request,$studentId){
            $email                  = (isset($request['Student_Email']) && $request['Student_Email'] !='' ? $request['Student_Email'] : false);
            $Student_Name           = (isset($request['Student_Name']) && $request['Student_Name'] !='' ? $request['Student_Name'] : 'Student Support Ticket');
            $request['Student_Id']  = (isset($request['studentId']) && $request['studentId'] !='' ? $request['studentId'] : $studentId);
            $Country                = (isset($request['Country']) && $request['Country'] !='' ? $request['Country'] : '');
            if($Country == ''){
                $Country = getCompanyInfo()->find(1)->value('Default_Country_Id');
            }
            $body                   = array('data'=>$request);

            $countryInfo            = $this->_mdlCountry::select('Country_Name')->find($Country);
            $request['Country']     = $countryInfo['Country_Name'];
            $this->SupportEmail     = getCompanyInfo()->find(1)->value('Company_Support_Email');


            if(!isset($body['data']['Subject'])){
                throw new Exception("Subject of ticket is missing", 404);
            }

            // $subject                = $this->MailSubject;
            $subject                = $body['data']['Subject'];
            $mailTemplate           = 'support';
            $fromName               = $Student_Name;
            if($email == false){
                throw new Exception("No Student Email found", 404);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Student email format is not proper ", 404);
            }

            if(!isset($body['data']['Student_Name'])){
                throw new Exception("Student Name is missing", 404);
            }
            if(!isset($body['data']['Department'])){
                throw new Exception("Department Name is missing", 404);
            }

            if(!isset($body['data']['Message'])){
                throw new Exception("Ticket message is missing", 404);
            }

            sendEmail($this->SupportEmail,$this->SupportToName,$email,$fromName,$subject,$body,$mailTemplate);

            //$response = $this->_mdlEnrollmentRequest->create($request);
            $this->successResponse['data'] =  $this->successResponse;

    }

}
