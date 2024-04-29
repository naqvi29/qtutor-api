<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ctrl_ZohoAuth extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $requestData = $request->query();

        $enPoint    = 'https://accounts.zoho.com/oauth/v2/auth';
        $requestData0 = array('response_type'=>$requestData['response_type'],'client_id'=>$requestData['client_id'],'scope'=>$requestData['scope'],'redirect_uri'=>$requestData['redirect_uri']);
            $param = '';
            if(sizeof($requestData0) >0){
                $string = '';
                foreach ($requestData0 as $key => $value) {
                    $string .=$key.'='.$value;
                    $string .='&';
                }
                $param = substr($string, 0, -1);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $enPoint.'?'.$param);
            curl_setopt($ch,CURLOPT_POST, 0);
            curl_setopt($ch,CURLOPT_HEADER, TRUE);
            curl_setopt($ch,CURLOPT_NOBODY, TRUE); // remove body
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, FALSE);
            // curl_setopt($ch,CURLOPT_HTTPHEADER, array('Authorization: '.$apiKey));
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $info = curl_getinfo($ch);
            curl_close($ch);
            // dd($result);
            return $result;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request0 = $request->query();
            $attemptToWriteText = $request0;

            Storage::put('attempt3.txt', $attemptToWriteText);
       } catch (\Exception $e) {
            dd($e);
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
