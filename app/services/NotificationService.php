<?php
namespace App\services;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Illuminate\Support\Str;
use LaravelFCM\Facades\FCM;

class NotificationService {

    public function broadcastNotification($data,$sendTo,$notificaiontData){
        $method ='';
        $optionBuilder       = new OptionsBuilder();
        $notificationBuilder = new PayloadNotificationBuilder($data['title']);

        foreach ($data as $key => $value) {
        $method =  'set'.ucfirst(Str::camel($key));
        $notificationBuilder->$method($value);
        }
        $optionBuilder->setTimeToLive(60*20);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($notificaiontData);

        $optionBuilder->setContentAvailable(true);
        $option = $optionBuilder->build();

        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();



        //$sendTo = ["chbxm4CwQ5WNcX0hujdy1v:APA91bEWCxyZhOPpvg3nm5AeSIshYpk_2qI5zuzPfZ63xYWMVZXciEBMty80wRsWYt5h8y2AcRDumAScZVaZSp2Sm5lmK-bgPMAbW_JcFR_6MKApv5D6P_HekJypQuyXcaZTNz0slucS"];
        $downstreamResponse = FCM::sendTo($sendTo, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        $downstreamResponse->tokensToDelete();

        $downstreamResponse->tokensToModify();

        $downstreamResponse->tokensToRetry();

        $downstreamResponse->tokensWithError();

        return $downstreamResponse;
    }

}
