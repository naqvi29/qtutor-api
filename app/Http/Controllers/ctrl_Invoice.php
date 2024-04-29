<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

class ctrl_Invoice extends Controller
{
    private   $_mdlIntegration;
    private   $_mdlIntegrationConfig;
    private   $_mdlUsers;
    public    $response;
    public    $endPointUrl     = array('invoice'=>'api/v3/invoices','token'=>'oauth/v2/token');
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        $this->_mdlIntegration       = getModel('Integration');//Helper function
        $this->_mdlIntegrationConfig = getModel('Integration_Config');//Helper function
        $this->_mdlUsers             = getModel('Users');//Helper function
    }


    public function index(Request $request,int $studentId) {

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

        $studentInfo = $this->getStudentInfo($studentId);

        if(!isset($dataRequst['queryString']['email'])){
            $dataRequst['queryString']['email'] = $studentInfo->email;
        }else{
            if($dataRequst['queryString']['email']  != $studentInfo->email){
                throw new Exception("Email is not the same for this student", 403);
            }
        }

       //pass data to  private function
       $this->getInvoice($dataRequst);

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

    public function show(Request $request,int $studentId,int $id) {

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

       //pass data to  private function
       $this->getInvoice($dataRequst,$id);

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

    private function getInvoice($requestedParams,$id=false){

        $integrationData    = $this->_mdlIntegration::get()->first();
        $integId            = $integrationData->Integration_Id;
        $apiUrl             = $integrationData->Integration_Api_Url;
        $organizationId     = $integrationData->Organization_Id;
        $clientId           = $integrationData->Client_Id;
        $clientSecret       = $integrationData->Client_Secret;
        $redirectUrl        = $integrationData->Redirect_Url;

        $integrationConfig  = $this->_mdlIntegrationConfig::where('Active',1)->where('Integration_Id',$integId)->first();
        $accessToken        = $integrationConfig->Access_Token;
        $refreshToken       = $integrationConfig->Refresh_Token;
        $endPointUrl        = $apiUrl.'/'.$this->endPointUrl['invoice'];
        if($id){
            $endPointUrl        = $apiUrl.'/'.$this->endPointUrl['invoice'].'/'.$id;
        }


        $configData         = array('accessToken'=>$accessToken,'refreshToken'=>$refreshToken,'organizationId'=>$organizationId);
        $apiResponse        = $this->getApiEndData($endPointUrl,$configData,$requestedParams['queryString']);

        if($apiResponse->code==57){
            $endPointUrlToken   = 'https://accounts.zoho.com'.'/'.$this->endPointUrl['token'];
            $requestedData      = array('refresh_token'=>$refreshToken,'client_id'=>$clientId,'client_secret'=>$clientSecret,'redirect_uri'=>$redirectUrl,'grant_type'=>'refresh_token');
            $newAccessToken     = $this->getValidToken($endPointUrlToken,$requestedData);

            if(isset($newAccessToken->access_token)){

                $this->_mdlIntegrationConfig::where('Active',1)->where('Integration_Id',$integId)->where('Refresh_Token',$refreshToken)->update(array('Access_Token'=>$newAccessToken->access_token));
                $requestedData  = array('accessToken'=>$newAccessToken->access_token,'refreshToken'=>$refreshToken,'organizationId'=>$organizationId);
                $apiResponse = $this->getApiEndData($endPointUrl,$requestedData,$requestedParams['queryString']);

            }
            else{

            }

        }
        if(!isset($apiResponse->invoices) && !isset($apiResponse->invoice) ){
            throw new Exception($apiResponse->message, 502);
        }

        if($id){
            $response = $apiResponse->invoice;
        }else{
            $response = $apiResponse->invoices;
        }
        if(is_array($response)){
                foreach ($response as $key => $value) {
                    switch($value->status){
                        case 'paid':
                            $response[$key]->status = 'paid';
                        break;
                        case 'draft':
                            foreach ($value as $key0 => $value0) {
                                unset($response[$key]->$key0);
                            }
                        break;
                        case 'void':
                            foreach ($value as $key0 => $value0) {
                                unset($response[$key]->$key0);
                            }
                        break;
                        default:
                            $response[$key]->status = 'unpaid';
                        break;
                    }
                }
        }else{
            switch($response->status){
                case 'paid':
                    $response->status = 'paid';
                break;
                default:
                    $response->status = 'unpaid';
                break;
            }
        }
        $mainRespons  = array_values(array_filter($response, function($a) { $arr =(array)$a;return $arr; }));
        $this->successResponse['data'] = $mainRespons;

    }

    private function getApiEndData($enPoint,$configData,$queryData=array()){

        $accessToken    = $configData['accessToken'];
        $refreshToken   = $configData['refreshToken'];
        $organizationId = $configData['organizationId'];
        $param = '';
        if(sizeof($queryData) >0){
            $string = '';
            foreach ($queryData as $key => $value) {
                $string .=$key.'='.$value;
                $string .='&';
            }
            $param = substr($string, 0, -1);
            $param = '?'.$param;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $enPoint.$param,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
              "Authorization: Zoho-oauthtoken $accessToken",
              "X-com-zoho-invoice-organizationid: $organizationId",
              "accept: application/json"
            ],
          ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return json_decode($response);
    }

    private function getValidToken($endPointUrl,$requestedData){

        $param = '';
        if(sizeof($requestedData) >0){
            $string = '';
            foreach ($requestedData as $key => $value) {
                $string .=$key.'='.$value;
                $string .='&';
            }
            $param = substr($string, 0, -1);
        }

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $endPointUrl.'?'.$param);
        curl_setopt($ch,CURLOPT_HEADER, false);
        curl_setopt($ch,CURLOPT_NOBODY, false); // remove body
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch,CURLOPT_POSTFIELDS, $requestedData);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_HTTPHEADER, array(
            'Content-Type: multipart/form-data; boundary=<calculated when request is sent>'
            )
        );
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return json_decode($result);
    }

    private function getStudentInfo($studentId){

        if($studentId !=false){
            $data  = $this->_mdlUsers::where('StudentId','=',$studentId)->first();
        }
        else{
            $data  = $this->_mdlUsers::get();
        }


        if($data == null || is_null($data) || blank($data)){
            throw new Exception('No data Found', 404);
        }

        return $data;

    }
}
