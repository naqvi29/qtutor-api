<?php

//MA - To get model(s) dunamically
if(!function_exists('getModel')){
    function getModel($model){
        $model = "App\Models"."\\".'mdl_'.$model;
        return new $model();
    }

}

//MA - To manage incoming request data
if(!function_exists('manageRequestData')){

    function manageRequestData($request,$setRequestHanlder=False){
        $data = [];

        $clientInfo = (isset($request['ClientInfo']) ? $request['ClientInfo'] : 'No Client Info Recorded');
        unset($request['ClientInfo']);

        if($setRequestHanlder == True){
            $requestBody = requestHandler($request->all());
        }else{
            $requestBody = $request->all();
        }
        $queryParam = $request->query();
        $data = [
            'reqBody'   =>$requestBody,
            'clientInfo'=>$clientInfo,
            'queryString'=> $queryParam
        ];
        return $data;
    }
}



if(!function_exists('createUserAccessToken')){
    function createUserAccessToken($data){

        if(Auth::attempt($data)){
            $accessToken = Auth::User()->createToken('authToken')->accessToken;
            $userId      = Auth::id();
            $response    = ['status'=>'success','code'=>'200','userId'=>$userId,'accessToken'=>$accessToken];
        }else{
            $response    = ['status'=>'failed','code'=>'401','message'=>'Invalid Username or Password'];
        }
        return $response;
    }

}

if(!function_exists('destroyAccessToken')){

    function destroyAccessToken($data,$isRevoked =false){
        if(Auth::check()){
            if($isRevoked == true){
                $request->user()->token()->revoke();
            }else{
                $deletedTokenId = $data->user()->token()->id;
                $data->user()->token()->delete();
                Auth::user()->AauthAcessToken()->where('id','=',$deletedTokenId)->delete();
            }
            $response   = ['status'=>'success','code'=>200,'message'=>'Token removed'];
        }
        else{
            $response   = ['status'=>'failed','code'=>403,'message'=>'Access denied due to invalid credentials'];
        }
        return $response;
    }

}


if(!function_exists('getCompanyInfo')){

    function getCompanyInfo(){
        return getModel('Company');
    }

}

if(!function_exists('setPlansMetaData')){

    function setPlansMetaData($data,$courseId,$countryId,$countryCurrency){

        $response    = array();
        $sizeOfPlans = sizeof($data);
        if($sizeOfPlans > 0){
            for ($i=0; $i < $sizeOfPlans; $i++) {
                $sizeofPlansMeta = sizeof($data->toArray()[$i]['plans_meta']);
                if($sizeofPlansMeta > 0){
                    $response[$i]=array('Course_Id'=>$courseId,'Country_Id'=>$countryId,'Plan_Name'=>$data->toArray()[$i]['Plan_Name']);
                    for ($j=0; $j < $sizeofPlansMeta; $j++) {

                        if($data->toArray()[$i]['plans_meta'][$j]['Meta_Title'] == 'Session Per week'){
                            $response[$i]['HeadingText'] =  $data->toArray()[$i]['plans_meta'][$j]['Meta_Value'].' Days Per week';
                        }

                        $isFeesValue = strpos($data->toArray()[$i]['plans_meta'][$j]['Meta_Title'],'Fee');

                        if(isset($isFeesValue) && is_numeric($isFeesValue)){

                            $response[$i][$data->toArray()[$i]['plans_meta'][$j]['Meta_Title']] = $countryCurrency.' '.$data->toArray()[$i]['plans_meta'][$j]['Meta_Value'] ;
                        }
                        else{
                            $response[$i][$data->toArray()[$i]['plans_meta'][$j]['Meta_Title']] = $data->toArray()[$i]['plans_meta'][$j]['Meta_Value'] ;
                        }

                    }

                }

            }
        }
        return $response;
    }

}

if(!function_exists('sendEmail')){

    function sendEmail($to,$toName,$from,$fromName,$subject,$body,$mailTemplate){

        return Mail::send(['html'=>$mailTemplate], $body, function($message) use($to,$toName,$from,$fromName,$subject){
            $message->to($to, $toName)->subject($subject);
            $message->from($from,$fromName);
            $message->getHeaders()->addTextHeader('x-mailgun-native-send', 'true');
        });

    }

}

if(!function_exists('checkForParam')){

    function checkForParam($queryParam){
        $name  =  (isset($queryParam['query']) ? $queryParam['query'] : false );

        if($name == false){
            return false;
        }
        return array('Country_Name'=>$name);
    }

}



