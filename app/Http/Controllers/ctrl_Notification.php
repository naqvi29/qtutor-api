<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Exception;

class ctrl_Notification extends Controller
{
    private $_mdlTokens;    //Model Name
    private $_mdlUsers;    //Model Name
    private $_mdlEnrCourse;    //Model Name
    private $_mdlBulletin;    //Model Name
    public  $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public  $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public  $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        $this->_mdlTokens   =getModel('NotificationToken');
        $this->_mdlUsers    =getModel('Users');
        $this->_mdlEnrCourse=getModel('EnrolledCourses');
        $this->_mdlBulletin =getModel('Bulletin');
    }
    public function index(Request $request){
        $dataRequst     = manageRequestData($request);
        $data           = $dataRequst['reqBody'];
        return $this->sendNotificationByToken($data);
    }

    public function createToken(Request $request){
        $dataRequst     = manageRequestData($request);
        $data           = $dataRequst['reqBody'];

        if(!isset($data['Token'])){
            $this->errorResponse['message']      ='Required Token param is missing';
            $this->errorResponse['errorCode'] ='401';
            $this->errorResponse['code'] ='401';
            return $this->errorResponse;
        }
        $this->storeToken($data);

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

    public function sendNotificationToStudents(Request $request,$sutdentId=false){
        $dataRequst     = manageRequestData($request);
        $data           = $dataRequst['reqBody'];
        $tokens         = array();
        if(!$sutdentId){
            $studentTokens  = $this->_mdlTokens::whereNotNull('Student_Id')->get();
        }else{
            $studentTokens  = $this->_mdlTokens::where('Student_Id','=',$sutdentId)->get();
        }

        if(blank($studentTokens)){
            throw new Exception("No Token Data Found", 404);
        }

        $distinctStudentId = $this->DistinctStudents($studentTokens->toArray());

        $this->storeNotificationToBulletin($distinctStudentId,$request);

        foreach ($studentTokens as $key => $value) {
            $tokens[] = $studentTokens[$key]->Token;
        }
        if(blank($tokens)){
            $this->errorResponse['data'] = 'No Student found';
            return $this->errorResponse;
        }

        $request['to'] = $tokens;

       return $this->sendNotificationByToken($request);

    }

    public function sendNotificationToGuest(Request $request){
        $dataRequst     = manageRequestData($request);
        $data           = $dataRequst['reqBody'];
        $tokens         = array();

        $guestTokens    = $this->_mdlTokens::whereNull('Student_Id')->get();
        foreach ($guestTokens as $key => $value) {
            $tokens[] = $guestTokens[$key]->Token;
        }

        if(blank($tokens)){
            $this->errorResponse['data'] = 'No Guest Tokens found';
            return $this->errorResponse;
        }

        $request['to'] = $tokens;
        return $this->sendNotificationByToken($request);

    }

    public function sendNotificationToCountry(Request $request,$countryId){
        $dataRequst     = manageRequestData($request);
        $data           = $dataRequst['reqBody'];
        $tokens         = array();

        $student    = $this->_mdlUsers::select('StudentId')->where('CountryID','=',$countryId)->get();
        if(blank($student)){
            throw new Exception("No Data found", 404);
        }
        $studentIds = $student->toArray();

        $studentTokens    = $this->_mdlTokens::whereIn('Student_Id', $studentIds)->get();

        if(blank($studentTokens)){
            throw new Exception("No Data found", 404);
        }
        $distinctStudentId = $this->DistinctStudents($studentTokens->toArray());

        $this->storeNotificationToBulletin($distinctStudentId,$request);

        foreach ($studentTokens as $key => $value) {
            $tokens[] = $studentTokens[$key]->Token;
        }

        if(blank($tokens)){
            $this->errorResponse['data'] = 'No Student Tokens found';
            return $this->errorResponse;
        }

        $request['to'] = $tokens;
        return $this->sendNotificationByToken($request);

    }

    public function sendNotificationTutorToStudent(Request $request,$tutorId){
        $dataRequst     = manageRequestData($request);
        $data           = $dataRequst['reqBody'];
        $tokens         = array();

        $student    = $this->_mdlEnrCourse::select('StudentId')->where('TutorId','=',$tutorId)->get();

        if(blank($student)){
            throw new Exception("No Students found for this tutor", 404);
        }
        $studentIds = $student->toArray();

        $studentTokens    = $this->_mdlTokens::whereIn('Student_Id', $studentIds)->get();

        if(blank($studentTokens)){
            throw new Exception("No Data found", 404);
        }
        $distinctStudentId = $this->DistinctStudents($studentTokens->toArray());

        $this->storeNotificationToBulletin($distinctStudentId,$request);

        foreach ($studentTokens as $key => $value) {
            $tokens[] = $studentTokens[$key]->Token;
        }

        if(blank($tokens)){
            $this->errorResponse['data'] = 'No Student Tokens found';
            return $this->errorResponse;
        }

        $request['to'] = $tokens;
        return $this->sendNotificationByToken($request);

    }

    public function sendNotificationTutorCourseToStudent(Request $request,$tutorId,$courseId){
        $dataRequst     = manageRequestData($request);
        $data           = $dataRequst['reqBody'];
        $tokens         = array();

        $student    = $this->_mdlEnrCourse::select('StudentId')->where('TutorId','=',$tutorId)->where('CourseId','=',$courseId)->get();

        if(blank($student)){
            throw new Exception("No Students found for this tutor and course", 404);
        }
        $studentIds = $student->toArray();

        $studentTokens    = $this->_mdlTokens::whereIn('Student_Id', $studentIds)->get();

        if(blank($studentTokens)){
            throw new Exception("No Data found", 404);
        }
        $distinctStudentId = $this->DistinctStudents($studentTokens->toArray());

        $this->storeNotificationToBulletin($distinctStudentId,$request);

        foreach ($studentTokens as $key => $value) {
            $tokens[] = $studentTokens[$key]->Token;
        }

        if(blank($tokens)){
            $this->errorResponse['data'] = 'No Student Tokens found';
            return $this->errorResponse;
        }

        $request['to'] = $tokens;
        return $this->sendNotificationByToken($request);

    }

    public function sendNotificationCourseToStudent(Request $request,$courseId){
        $dataRequst     = manageRequestData($request);
        $data           = $dataRequst['reqBody'];
        $tokens         = array();

        $student    = $this->_mdlEnrCourse::select('StudentId')->where('CourseId','=',$courseId)->get();

        if(blank($student)){
            throw new Exception("No Students found for this course", 404);
        }
        $studentIds = $student->toArray();

        $studentTokens    = $this->_mdlTokens::whereIn('Student_Id', $studentIds)->get();

        if(blank($studentTokens)){
            throw new Exception("No Student Token Data found", 404);
        }
        $distinctStudentId = $this->DistinctStudents($studentTokens->toArray());

        $this->storeNotificationToBulletin($distinctStudentId,$request);

        foreach ($studentTokens as $key => $value) {
            $tokens[] = $studentTokens[$key]->Token;
        }

        if(blank($tokens)){
            $this->errorResponse['data'] = 'No Student Tokens found';
            return $this->errorResponse;
        }

        $request['to'] = $tokens;
        return $this->sendNotificationByToken($request);

    }

    public function sendNotificationToAll(Request $request){
        $dataRequst     = manageRequestData($request);
        $data           = $dataRequst['reqBody'];
        $tokens         = array();
        $allTokens      = $this->_mdlTokens::get();

        $distinctStudentId = $this->DistinctStudents($allTokens->toArray());

        $this->storeNotificationToBulletin($distinctStudentId,$request);

        foreach ($allTokens as $key => $value) {
            $tokens[]     = $allTokens[$key]->Token;
        }

        if(blank($tokens)){
            $this->errorResponse['data'] = 'No Tokens found';
            return $this->errorResponse;
        }

        $request['to'] = $tokens;
        return $this->sendNotificationByToken($request);

    }

    private function storeToken($request){

        $removeToken = $this->_mdlTokens::where('Token', '=', $request['Token'])->delete();

        $data = $this->_mdlTokens::updateOrCreate($request);

        $this->successResponse['data'] =  $data;

    }

    private function storeNotificationToBulletin($studentIds,$notificationData){
        $description      = $notificationData['notification']['body'];
        $title            = $notificationData['notification']['title'];

        foreach ($studentIds as $key => $value) {
            $StudentId =  $value['Student_Id'];
            if($StudentId!='' && $StudentId != null){
             $bulletinToCreate = array('ind_notification'=>'<h2>'.$title.'</h2>'.'<p>'.$description.'</p>','StudentID'=>$StudentId,'endDate'=>date('Y-m-d'),'Is_Read'=>0);
             $this->_mdlBulletin::create($bulletinToCreate);
            }
        }
    }

    public function sendNotificationByToken($request){

        // $dataRequst     = manageRequestData($request);
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
        $notificationData     = ((isset($notification['data']) && $notification['data'] !='') ? $notification['data'] : array('landingUrl'=>'bulletin'));

        $notification = new \App\services\NotificationService();
        $repsonse     = $notification->broadcastNotification($data,$sendTo,$notificationData);
        $mainResponse = $this->fcmRemoveTrashToken($repsonse);
        $this->successResponse['data'] = $repsonse;
        return $this->successResponse;
    }


    private function DistinctStudents($array){
        $stdNum =array();
        $tmp=0;
        $i=0;
        foreach ($array as $key => $value) {
            $i++;
                $studentId = $value['Student_Id'];
                if($tmp!=$studentId || $i==1){
                    $stdNum[$key]['Student_Id'] = $studentId;
                }
                $tmp = $studentId;
        }
        return $stdNum;
    }

    private function fcmRemoveTrashToken($request){
        $deleteToken  = $request->tokensToDelete();
        $errorToken   = $request->tokensWithError();
        $istokenDeleted = $this->_mdlTokens::whereIn('Token',$deleteToken)->delete();
    }
}
