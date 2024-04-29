<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use \Illuminate\Database\QueryException;
use DB;

class ctrl_Categories extends Controller
{
    //Variable init
    private $_mdlCourseCategories;
    public  $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];


    //Load model on class load
    public function __construct(){
        $this->_mdlCourseCategories=getModel('CourseCategories');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // Get Request: Category list
    public function index(Request $request)
    {
        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        //Sending data private function
        $this->getCategories($data);

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

       //pass data to  private function
       $this->getCategories($data,$id);

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

    private function getCategories($request,$id=false){
        $baseCategoryPath =  url('storage/assets/images/category/');

            if($id==false && isset($request['featured']) && $request['featured'] == true){

                //To get featured Categoires
                //  $data = $this->_mdlCourseCategories::where('Is_Featured','=',1)->get();
                 $data  = $this->_mdlCourseCategories::select('Category_Id','Category_Name','Category_Title','Category_Description','Category_Type',DB::raw("CONCAT('$baseCategoryPath','/',Category_Image) AS Category_Image"),"Is_Featured","Active","Created_On","Created_By")->where('Is_Featured','=',1)->get();
            }
            else if($id==false && (!isset($request['featured']) || $request['featured'] != true)){

                //To get all categories
                // $data = $this->_mdlCourseCategories::get();
                $data  = $this->_mdlCourseCategories::select('Category_Id','Category_Name','Category_Title','Category_Description','Category_Type',DB::raw("CONCAT('$baseCategoryPath','/',Category_Image) AS Category_Image"),"Is_Featured","Active","Created_On","Created_By")->get();
            }
            else{

                //To get single Categoires
                //  $data = $this->_mdlCourseCategories::find($id);
                 $data  = $this->_mdlCourseCategories::select('Category_Id','Category_Name','Category_Title','Category_Description','Category_Type',DB::raw("CONCAT('$baseCategoryPath','/',Category_Image) AS Category_Image"),"Is_Featured","Active","Created_On","Created_By")->where('Category_Id','=',$id)->get();

            }
            if(blank($data)){
                throw new Exception('No data Found', 404);
            }
            $this->successResponse['data'] =  $data;

    }

}

