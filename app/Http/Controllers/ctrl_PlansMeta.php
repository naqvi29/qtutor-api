<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Exception;
use \Illuminate\Database\QueryException;

class ctrl_PlansMeta extends Controller
{
    private   $_mdlPlansMeta;
    private   $_mdlPlans;
    private   $_mdlCountry;
    public    $response;
    protected $errorReporting  = true;
    public    $errorResponse   = ['status'=>'failed','code'=>401,'message'=>'Error while processing your request!','errorCode'=>200];
    public    $warningResponse = ['status'=>'info','code'=>500,'message'=>'Method not supported'];
    public    $successResponse = ['status'=>'success','code'=>200,'message'=>'Your request has been pocessed successfully'];

    public function __construct(){
        $this->_mdlPlansMeta=getModel('PlansMeta');//Helper function
        $this->_mdlPlans    = getModel('Plans');
        $this->_mdlCountry  = getModel('Country');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,int $courseId=0)
    {

        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        if(!is_numeric($courseId)){
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
       $this->getPlansMeta($data,$courseId);

        // Return success response from public function
        if(isset($this->errorResponse['errorCode']) && $this->errorResponse['errorCode'] != 200){
            $this->response =  $this->errorResponse;
        }

        //v1.0 - If any success occur
        if(isset($this->successResponse['data']) && $this->errorResponse['errorCode'] == 200){
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
    public function show(Request $request,$courseId,$countryId=false,$planId=false)
    {

        //Handle incoming request and client info
        $dataRequst     = manageRequestData($request);

        $data           = $dataRequst['reqBody'];
        $this->errorReporting = (isset($data['errorReporting']) && $data['errorReporting'] == true? false:true);
        unset($data['errorReporting']);

        if(!isset($courseId) || !is_numeric($courseId)){
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
        $getDefaultPlans = false;
        $isCountryPlansAvailable = $this->_mdlPlansMeta::where('Course_Id','=',$courseId)->where('Country_Id','=',$countryId)->count();

        if($isCountryPlansAvailable == 0){
            $getDefaultPlans = true;
        }

        //pass data to get categories private function
       $this->getPlansMeta($data,$courseId,$countryId,$planId,$getDefaultPlans);

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

    private function getPlansMeta($request, $courseId, $countryId = false, $planId = false,$defaultPlans=false){
        // DB::connection()->enableQueryLog();

        // $data  = $this->_mdlPlans::select('Plan_Id','Plan_Name')
        // ->with('plansMeta', function ($query) use($courseId,$countryId){
        //     return $query->where('Course_Id', '=', $courseId)
        //                  ->where('Country_Id', '=', $countryId)
        //                  ->groupBy('Plan_Id','Meta_Name');
        // })
        // ->whereHas('plansMeta', function ($query) use($courseId,$countryId){
        //     return $query->where('Course_Id', '=', $courseId)
        //                  ->where('Country_Id', '=', $countryId);
        // })->get();
        // $queries = DB::getQueryLog();
        // print_r($queries);
        // dd($queries);
        if(!$defaultPlans){
            switch($planId){
                case !false:
                    $data  = $this->_mdlPlans::select('Plan_Id','Plan_Name')->with('plansMeta', function ($query) use($courseId,$countryId,$planId){
                        return $query->where('Course_Id', '=', $courseId)->where('Plan_Id', '=', $planId)
                                     ->where('Country_Id', '=', $countryId)
                                     ->groupBy('Country_Id','Plan_Id','Meta_Name');
                    })->where('Plan_Id', '=', $planId)->get();
                break;

                case false:
                    $data  = $this->_mdlPlans::select('Plan_Id','Plan_Name')
                    ->with('plansMeta', function ($query) use($courseId,$countryId){
                        return $query->where('Course_Id', '=', $courseId)
                        ->where('Country_Id', '=', $countryId)
                        ->groupBy('Plan_Id','Meta_Name');
                    })
                    ->whereHas('plansMeta', function ($query) use($courseId,$countryId){
                        return $query->where('Course_Id', '=', $courseId)
                                     ->where('Country_Id', '=', $countryId);
                    })->get();
                break;
                default:
                    $data  = $this->_mdlPlans::select('Plan_Id','Plan_Name')->with('plansMeta', function ($query) use($courseId){
                        return $query->select('Plan_Id','Course_Id','Country_Id','Meta_Title','Meta_Value')->where('Course_Id', '=', $courseId)->groupBy('Country_Id','Plan_Id','Meta_Name','Meta_Title');
                    })->get();
            }
        }
        else{
            $companyDefaultCountry = getCompanyInfo()->find(1)['Default_Country_Id'];

            $data  = $this->_mdlPlans::select('Plan_Id','Plan_Name')->with('plansMeta', function ($query) use($courseId,$companyDefaultCountry){
                return $query->where('Course_Id', '=', $courseId)->where('Country_Id', '=', $companyDefaultCountry)->groupBy('Plan_Id','Meta_Name');
            })->get();
        }


        $countryInfo = $this->_mdlCountry::find($countryId);
        $countryCurrency = $countryInfo['Country_Currency'];

        $response = setPlansMetaData($data,$courseId,$countryId,$countryCurrency);

        if($response == null || is_null($response) || blank($response)){
            throw new Exception('No data Found', 404);
        }

        $data     = $response;

        $this->successResponse['data'] =  $data;


    }


}
