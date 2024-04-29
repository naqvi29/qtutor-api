<?php
/*
|--------------------------------------------------------------------------
| User Controller
|--------------------------------------------------------------------------
|
| This controller will handle all user operations, Like login | logout | Signup.
|
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Exception;
use Illuminate\Auth\AuthenticationException;
use \Illuminate\Database\QueryException;
use DB;

class ctrl_User extends Controller
{
    //MA - Defining Default Access Modifiers
    private   $_mdlUser;
    private   $_mdlParent;
    public    $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        $this->_mdlUser   = getModel('Users');//Helper function
        $this->_mdlParent = getModel('Parent');//Helper function
    }

    // Login public function to handle incoming request and return response
    public function login(Request $request){

        // Hangle incoming request , response will be array having data and client info
        $dataRequst           = manageRequestData($request); // Helper function

        // Categorized request data
        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        // Validating login request data
        $isValidated    = $this->validateStudentLoginData($data);

        // If validation failed
        if(isset($isValidated['status']) && $isValidated['status'] == FALSE) {

            $errorCode                        = 400;
            $errorMessage                     = 'Required field(s) are missing';
            $message                          = "User Name or Passowrd missing";
            $this->errorResponse['errorCode'] = 400;

            if($this->errorReporting == true){
                $message         = (isset($isValidated['data']->Messages()->getMessages()['username']) ? $isValidated['data']->Messages()->getMessages()['username'] : $isValidated['data']->Messages()->getMessages()['password']);
                $this->errorResponse['error']     = array('errorMessage'=>$errorMessage,'message'=>$message);
            }
            return $this->errorResponse;
        }

        //Sending data to login private function
        $this->loginUser($data);

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


    public function loginParent(Request $request){

        // Hangle incoming request , response will be array having data and client info
        $dataRequst           = manageRequestData($request); // Helper function

        // Categorized request data
        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);


        // Validating login request data
        $isValidated    = $this->validateParentLoginData($data);

        // If validation failed
        if(isset($isValidated['status']) && $isValidated['status'] == FALSE) {

            $errorCode                        = 400;
            $errorMessage                     = 'Required field(s) are missing';
            $message                          = "Email or Passowrd missing";
            $this->errorResponse['errorCode'] = 400;

            if($this->errorReporting == true){
                $message         = (isset($isValidated['data']->Messages()->getMessages()['email']) ? $isValidated['data']->Messages()->getMessages()['email'] : $isValidated['data']->Messages()->getMessages()['password']);
                $this->errorResponse['error']     = array('errorMessage'=>$errorMessage,'message'=>$message);
            }
            return $this->errorResponse;
        }

        //Sending data to login private function
        $this->authParent($data);

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


    // Logout public function to handle incoming request and return response, Destroy access token
    public function logout(Request $request){

        //Delete access token
        $response = destroyAccessToken($request); // Helper function

        $this->successResponse['data'] = $response;
        return $this->successResponse;
    }

    // Login private function to create access token and return response
    private function loginUser($request){

        $requestedData['username'] = $request['username'];
        $requestedData['password'] = $request['password'];

        //Authenticate user info and generate access token
        $response = createUserAccessToken($requestedData); // Helper function

        // If user is authenticated and token generated
        if($response['code'] == 200){

            $userInfo               = $this->_mdlUser::find($response['userId']);
            $companyInfo            = getCompanyInfo()->find(1);
            $company                = array('Company_Name'=>$companyInfo['Company_Name'],'Company_Terms'=>$companyInfo['Company_Terms'],'Company_Support_Terms'=>$companyInfo['Company_Support_Terms'],'Student_Support_Chat_URL'=>$companyInfo['Student_Support_Chat_URL'],'Guest_Support_Chat_URL'=>$companyInfo['Guest_Support_Chat_URL']);
            $data                   = ['user'=>$userInfo,'company'=>$company,'accessToken'=>$response['accessToken']];
            $this->successResponse['data'] =  $data;

        }else{
            // User authentication failed
            throw new Exception($response['message'],403);
        }

    }

    private function authParent($request){

        $requestedData['email']    = $request['email'];
        $requestedData['password'] = $request['password'];

        $response = $this->_mdlParent::where('guardianEmail','=',$request['email'])->where('password','=',md5($request['password']))->get();

        if(blank($response)){

            throw new Exception('Invalid Email or Password',403);
        }

        $studentId= (isset($response->toArray()[0]['StudentId']) ?$response->toArray()[0]['StudentId']: 0);
        $password = (isset($response->toArray()[0]['password']) ?$response->toArray()[0]['password']: 0);

        if($studentId<=0){
            throw new Exception('Invalid Email or Password',403);
        }
        else{

            $data = $this->_mdlUser::select('username',DB::raw("CONCAT('".$request['password']."') AS password"))->where('StudentId','=',$studentId)->where('password','=',$password)->get()->toArray()[0];

            //Authenticate user info and generate access token
            $response = createUserAccessToken($data); // Helper function

            // If user is authenticated and token generated

           if($response['code'] == 200){
               // $response0 = $this->_mdlParent::select(DB::raw("group_concat(StudentId) AS StudentId"))->where('guardianEmail','=',$request['email'])->get()->toArray();
               $response0   = $this->_mdlParent::select('StudentId')->where('guardianEmail','=',$request['email'])->get()->toArray();
                $userInfo    = $this->_mdlUser::select('*')->whereIn('StudentId',$response0)->get();
                $companyInfo            = getCompanyInfo()->find(1);
                $company                = array('Company_Name'=>$companyInfo['Company_Name'],'Company_Terms'=>$companyInfo['Company_Terms'],'Company_Support_Terms'=>$companyInfo['Company_Support_Terms'],'Student_Support_Chat_URL'=>$companyInfo['Student_Support_Chat_URL'],'Guest_Support_Chat_URL'=>$companyInfo['Guest_Support_Chat_URL']);
                $accessToken = Auth::User()->createToken(['userInfo' => $userInfo])->accessToken;

                $data                          = ['user'=>$userInfo,'company'=>$company,'accessToken'=>$accessToken];
                $this->successResponse['data'] =  $data;

            }
            else{
                // User authentication failed
                throw new Exception('Invalid Email or Password',403);
            }
        }

    }

    // validate incoming request for login
    private function validateStudentLoginData($data){

        $validated = TRUE;

        $validator = Validator::make($data, [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $validated = FALSE;
        }

        return array('status'=>$validated,'data'=>$validator);
    }


    // validate incoming request for login
    private function validateParentLoginData($data){

        $validated = TRUE;

        $validator = Validator::make($data, [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $validated = FALSE;
        }

        return array('status'=>$validated,'data'=>$validator);
    }

}
