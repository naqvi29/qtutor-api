<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use DB;
use Auth;
class ctrl_Students extends Controller
{
    private   $_mdlUsers;
    private   $_mdlParent;
    public    $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        $this->_mdlUsers = getModel('Users');//Helper function
        $this->_mdlParent = getModel('Parent');//Helper function
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
        $data['accessToken'] = $request->header('authorization');

        //pass data to  private function
       $this->getStudents($data);

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
    public function show(Request $request,$studentId)
    {

        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        if(!is_numeric($studentId)){
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
        $data['accessToken'] = $request->header('authorization');

       //pass data to private function
       $this->getStudents($data,$studentId);

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


    // private function getStudents($request,$studentId=false){

    //     if($studentId !=false){
    //         $data  = $this->_mdlUsers::where('StudentId','=',$studentId)->get();
    //     }
    //     else{
    //         if(isset($request['email'])){
    //             $isEmailValid  = filter_var($request['email'], FILTER_VALIDATE_EMAIL );
    //             if(!$isEmailValid){ throw new Exception("Bad Request!, Invalid parent email", 413); }

    //             $parentData  = $this->_mdlParent::where('guardianEmail',$request['email'])->get();
    //             if(blank($parentData)){ throw new Exception("No Data found", 404);}

    //             $sql = $this->_mdlUsers->newQuery();

    //             foreach ($parentData as $key => $value) {
    //                 $studentId[] = $parentData[$key]->StudentId;
    //             }

    //             $sql->whereIn('StudentId',$studentId);
    //             $data = $sql->get();
    //         }
    //         else{
    //             $data  = $this->_mdlUsers::get();
    //         }

    //     }


    //     if($data == null || is_null($data) || blank($data)){
    //         throw new Exception('No data Found', 404);
    //     }

    //     $this->successResponse['data'] =  $data;

    // }


    private function getStudents($request,$studentId=false){
        if($studentId !=false){
            $data  = $this->_mdlUsers::where('StudentId','=',$studentId)->get()->first();
        }
        else{
            if(isset($request['email'])){
                $isEmailValid  = filter_var($request['email'], FILTER_VALIDATE_EMAIL );
                if(!$isEmailValid){ throw new Exception("Bad Request!, Invalid parent email", 413); }

                $parentData  = $this->_mdlParent::where('guardianEmail',$request['email'])->get();
                if(blank($parentData)){ throw new Exception("No Data found", 404);}
                $accessToken = str_replace('Bearer ','',$request['accessToken']);

                $response0   = $this->_mdlParent::select('StudentId')->where('guardianEmail','=',$request['email'])->get()->toArray();
                $userInfo    = $this->_mdlUsers::select('*')->whereIn('StudentId',$response0)->get();
                $companyInfo = getCompanyInfo()->find(1);
                $company     = array('Company_Name'=>$companyInfo['Company_Name'],'Company_Terms'=>$companyInfo['Company_Terms'],'Company_Support_Terms'=>$companyInfo['Company_Support_Terms']);

                $data        = ['user'=>$userInfo,'company'=>$company,'accessToken'=>$accessToken];

            }
            else{
                $data  = $this->_mdlUsers::get();
            }
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

        if($data == null || is_null($data) || blank($data)){
            throw new Exception('No data found', 404);
        }

        $this->successResponse['data'] =  $data;


    }

}

