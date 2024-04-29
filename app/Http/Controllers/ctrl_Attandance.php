<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use DB;
use DateTime;
use DateTimeZone;
class ctrl_Attandance extends Controller
{
    private   $_mdlAttendance;
    private   $_mdlAttendanceMessage;
    private   $_mdlCountry;
    private   $_mdlTokens;    //Model Name
    private   $_mdlUsers;    //Model Name
    public    $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];


    public function __construct(){
        $this->_mdlAttendance          = getModel('Attendance');//Helper function
        $this->_mdlAttendanceMessage   = getModel('AttendanceMessage');//Helper function
        $this->_mdlCountry             = getModel('Country');//Helper function
        $this->_mdlTokens              = getModel('NotificationToken');
        $this->_mdlUsers               = getModel('Users');

    }

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
       $this->getStudentAttendance($data,$studentId);

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

       //pass data to  private function
       $this->getStudentAttendance($data,$studentId,$id);

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

    public function storeMessage(Request $request,int $studentId,int $id)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

       //pass data to  private function
       $this->storeAttendanceMessage($data,$studentId,$id);

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

    public function storeMessageAdmin(Request $request,int $attendId)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

       //pass data to  private function
       $this->storeAttendanceMessageAdmin($data,$attendId);

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

    public function showAllMessages(Request $request,int $studentId,int $id)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

       //pass data to  private function
       $this->getAttendanceMessage($data,$studentId,$id);

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

    public function showMessageAllAdmin(Request $request,int $attendId)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

       //pass data to  private function
       $this->getAttendanceMessageAdmin($data,$attendId);

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

    public function showSelectedMessages(Request $request,int $studentId,int $attendId , int $id)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

       //pass data to  private function
       $this->getAttendanceMessage($data,$studentId,$attendId,$id);

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

    public function showMessageStudentAdmin(Request $request,int $attendId,int $studentId)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

       //pass data to  private function
       $this->getAttendanceMessageAdmin($data,$attendId,$studentId);

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

    public function editMessage(Request $request,int $studentId,int $attendId , int $id)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

       //pass data to  private function
       $this->updateMessage($data,$studentId,$attendId,$id);

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


    private function getAttendanceMessage($request,$studentId,$attendId,$id=false){

        if(!$id){
            $data  = $this->_mdlAttendanceMessage::where('Attendance_Id','=',$attendId)->where('Student_Id','=',$studentId)->get();
        }else{
            $data  = $this->_mdlAttendanceMessage::where('Attendance_Id','=',$attendId)->where('Student_Id','=',$studentId)->where('Messages_Id','=',$id)->get();
        }
        if($data == null || is_null($data) || blank($data)){
            throw new Exception('No data Found', 404);
        }

        // if(isset($request['countryId']) && $request['countryId'] !='' && is_numeric($request['countryId'])){
        //     $countryData = $this->_mdlCountry::find($request['countryId']);
        //     $timeZone    = $countryData->Country_Timezone;

        //     $tz = new DateTimeZone($timeZone);

        //     foreach ($data as $key => $value) {
        //         if($value->Created_On != null){
        //             $date = new DateTime($value->Created_On);
        //             $date->setTimezone($tz);
        //             $newDate=  $date->format('Y-m-d h:i:s');
        //             $value->Created_On = $newDate;
        //         }
        //     }

        // }


        $this->successResponse['data'] =  $data;

    }

    private function getAttendanceMessageAdmin($request,$attendId,$studentId=false){
        if(!$studentId){
            $data  = $this->_mdlAttendanceMessage::where('Attendance_Id','=',$attendId)->get();
        }else{
            $data  = $this->_mdlAttendanceMessage::where('Attendance_Id','=',$attendId)->where('Student_Id','=',$studentId)->get();
        }
        if($data == null || is_null($data) || blank($data)){
            throw new Exception('No data Found', 404);
        }
        $this->successResponse['data'] =  $data;

    }


    private function storeAttendanceMessageAdmin($request,$attendId){

        if(!isset($request['Class_Id']) || $request['Class_Id'] == '' || !isset($request['Student_Id']) || $request['Student_Id'] == ''){
            $attendanceData  = $this->_mdlAttendance::select('ClassID','StudentID')->where('id','=',$attendId)->first();

            if($attendanceData == null || is_null($attendanceData) || blank($attendanceData)){
                throw new Exception('No data Found', 404);
            }
            $request['Class_Id']   = $attendanceData->ClassID;
            $request['Student_Id'] = $attendanceData->StudentID;
        }

        $studentCountryId = $this->_mdlUsers::select('CountryID')->where('StudentId','=',$request['Student_Id'])->first();

        if($studentCountryId->CountryID>0 && is_numeric($studentCountryId->CountryID)){
            $countryData = $this->_mdlCountry::find($studentCountryId->CountryID);
            $timeZone    = $countryData->Country_Timezone;

            $datetime = new DateTime();
            $tz       = new DateTimeZone($timeZone);

            $datetime->setTimezone($tz);
            $newDate=  $datetime->format('Y-m-d H:i:s');

            $request['Created_On'] = $newDate;
        }

        $request['Message_From'] = 'admin';
        $request['Attendance_Id']= $attendId;

        $data  = $this->_mdlAttendanceMessage::create($request);
        $message = array('title'=>"New Feedback Message",'body'=>$request['Message'],'data'=>array('attendanceReportId'=>$request['Attendance_Id'],'studentId'=>$request['Student_Id'],'landingUrl'=>'attendanceReport'));
        $requestData = array('notification'=>$message);

        $this->sendNotificationToStudents($request['Student_Id'],$requestData);

        $this->successResponse['data'] =  $data;

    }

    private function storeAttendanceMessage($request,$studentId,$id){

        if(!isset($request['Class_Id']) || $request['Class_Id'] == ''){
            $attendanceData  = $this->_mdlAttendance::select('ClassID')->where('studentID','=',$studentId)->where('id','=',$id)->first();

            if($attendanceData == null || is_null($attendanceData) || blank($attendanceData)){
                throw new Exception('No data Found', 404);
            }
            $request['Class_Id'] = $attendanceData->ClassID;
        }
        $studentCountryId = $this->_mdlUsers::select('CountryID')->where('StudentId','=',$studentId)->first();

        if($studentCountryId->CountryID>0 && is_numeric($studentCountryId->CountryID)){
            $countryData = $this->_mdlCountry::find($studentCountryId->CountryID);
            $timeZone    = $countryData->Country_Timezone;

            $datetime = new DateTime();
            $tz       = new DateTimeZone($timeZone);

            $datetime->setTimezone($tz);
            $newDate=  $datetime->format('Y-m-d H:i:s');

            $request['Created_On'] = $newDate;
        }

        $request['Message_From'] = 'student';
        $request['Attendance_Id']= $id;
        $request['Student_Id']   = $studentId;

        $data  = $this->_mdlAttendanceMessage::create($request);

        $this->successResponse['data'] =  $data;

    }

    private function updateMessage($request,$studentId,$attendId,$id){

        $data  = $this->_mdlAttendanceMessage::where('Messages_Id','=',$id)->where('Attendance_Id','=',$attendId)->where('Student_Id','=',$studentId)->update($request);
        $this->getAttendanceMessage($request,$studentId,$attendId,$id);

    }


    private function getStudentAttendance($request,$studentId,$id=false){
        if(!$id){
            $data  = $this->_mdlAttendance::where('studentID','=',$studentId)->offset(0)->limit(7)->orderBy('id','DESC')->get();
        }else{
            $data  = $this->_mdlAttendance::where('studentID','=',$studentId)->where('id','=',$id)->get();
        }
        if($data == null || is_null($data) || blank($data)){
            throw new Exception('No data Found', 404);
        }

        foreach($data as $key=>$val){
            $concentrationGrade = $val->ConcentrationGrade;
            $assignmentGrade    = $val->AssignmentGrade;
            $effortsGrade       = $val->EffortsGrade;
            $punctualityGrade   = $val->PunctualityGrade;
            $progressGrade      = $val->ProgressGrade;

            if(!!$val->ClassDate){
                $val->ClassDate     = str_replace('PST','',$val->ClassDate); // Remove timezone conversation
                $val->ClassDate     = date("l, d M Y", strtotime($val->ClassDate));
            }

            $avg                = ($concentrationGrade + $assignmentGrade + $effortsGrade + $punctualityGrade +$progressGrade) / 5;
            $data[$key]->AverageRating= $avg;
        }

        $this->successResponse['data'] =  $data;

    }


    private function sendNotificationToStudents($sutdentId,$request){

        $tokens         = array();
        $studentTokens  = $this->_mdlTokens::where('Student_Id','=',$sutdentId)->get();
        foreach ($studentTokens as $key => $value) {
            $tokens[] = $studentTokens[$key]->Token;
        }

        $request['to'] = $tokens;

       $this->sendNotificationByToken($request);

    }

    private function sendNotificationByToken($request){

        $reqBody        = $request;

        $numberOfTokens = sizeof($reqBody['to']);

        if($numberOfTokens <= 0 ){
            throw new Exception("Required param : Token missing", 404);
        }

        if(!isset($reqBody['notification']['title']) || $reqBody['notification']['title'] ==''){
             throw new Exception("Required param : Title missing", 404);
        }

        $sendTo       = $reqBody['to'];
        $notification = $reqBody['notification'];

        $data['title']        = ((isset($notification['title']) && $notification['title'] !='') ? $notification['title'] : '');
        $data['body']         = ((isset($notification['body']) && $notification['body'] !='') ? $notification['body'] : '');
        $data['click_action'] = ((isset($notification['click_action']) && $notification['click_action'] !='') ? $notification['click_action'] : '');
        $data['sound']        = ((isset($notification['sound']) && $notification['sound'] !='') ? $notification['sound'] : '');
        $data['icon']         = ((isset($notification['icon']) && $notification['icon'] !='') ? $notification['icon'] : '');
        $data['badge']        = ((isset($notification['badge']) && $notification['badge'] !='') ? $notification['badge'] : 1);
        $data['image']        = ((isset($notification['image']) && $notification['image'] !='') ? $notification['image'] : '');
        $data['content_available']        = ((isset($notification['content_available'])) ? $notification['content_available']: true);
        $notificationData     = ((isset($notification['data']) && $notification['data'] !='') ? $notification['data'] : array());

        $notification = new \App\services\NotificationService();

        $response = $notification->broadcastNotification($data,$sendTo,$notificationData);
        $mainResponse = $this->fcmRemoveTrashToken($response);

    }

    private function fcmRemoveTrashToken($request){
        $deleteToken  = $request->tokensToDelete();
        $errorToken   = $request->tokensWithError();
        $istokenDeleted = $this->_mdlTokens::whereIn('Token',$deleteToken)->delete();
    }

}
