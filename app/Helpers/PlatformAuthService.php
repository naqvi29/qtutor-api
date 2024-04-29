<?php

namespace App\Helpers;
class PlatformAuthService{

    private $key             = "+_)(*&^$#95623SDFGJ/*-!@$#!@$#!@#!@156941891alrr=1";
    private $platFormKey     = ["1"=>"utge3eYDNaNTGRM3ZM5l4HjV5J7Nm51S7OtfXLWG","2"=>"iMYsA1STsMxJKitLoaKdszvQgt3rOvKJZhyEFGJy"];
    public  $validPlatform   = false;
    public  $clientInfo;
    public function __construct($request){
        $this->authentication($request);
    }


    private function authentication($request){
            $id  = ($request->header('x-cli-id') !=null ? $request->header('x-cli-id') : false);
            $key = ($request->header('x-cli-key')!=null ? $request->header('x-cli-key') : false);

            if(!$key || !$id ){
                return false;
            }
            if(!isset($this->platFormKey[$id]) || $this->platFormKey[$id] != $key){
                return false;
            }

            $this->clientInfo['Browser'] = $request->header('User-Agent'); //Get Browser
            $this->clientInfo['Ip']      =  $request->ip(); //Get Ip

            $this->validPlatform = true;

    }
}
