<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        $obj = get_class($exception);

        switch($obj){
            case 'Throwable':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0 ?$exception->getCode():501);
                $message   = $exception->getMessage();
                $errorCode = $code;
                $error     = array('status'=>$status,'code'=>$code,'message'=>$message,'errorCode'=>$errorCode,'error'=>array('errorMessage'=>$message,'message'=>$message));
                return response($error,$code);

            break;
            case 'Exception':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0 ?$exception->getCode():404);
                $message   = $exception->getMessage();
                $errorCode = $code;
                $error     = array('status'=>$status,'code'=>$code,'message'=>$message,'errorCode'=>$errorCode,'error'=>array('errorMessage'=>$message,'message'=>$message));
                return response($error,$code);
            break;
            case 'Illuminate\Database\QueryException':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0 ?$exception->getCode():501);
                $message   = $exception->getMessage();
                $errorCode = $code;
                $error     = array('status'=>$status,'code'=>$code,'message'=>$message,'errorCode'=>$errorCode,'error'=>array('errorMessage'=>$message,'message'=>$message));
                return response($error,$code);
            break;
            case 'HttpClientException':
                $status    = 'failed';
                $code      = ($exception->getCode() > 0 ?$exception->getCode():425);
                $message   = $exception->getMessage();
                $errorCode = $code;
                $error     = array('status'=>$status,'code'=>$code,'message'=>$message,'errorCode'=>$errorCode,'error'=>array('errorMessage'=>$message,'message'=>$message));
                return response($error,$code);
            break;
            case 'Illuminate\Auth\AuthenticationException':
                $status         = 'failed';
                $code           = ($exception->getCode() > 0 ?$exception->getCode():403);
                $message        = $exception->getMessage();
                $errorMessage   = 'Session has been expired! Kindly login again.';
                $errorCode      = $code;
                $error          = array('status'=>$status,'code'=>$code,'message'=>$errorMessage,'errorCode'=>$errorCode,'error'=>array('errorMessage'=>$message,'message'=>$errorMessage));
                return response($error,$code);
            break;
            default:
                $status    = 'failed';
                $code      = ($exception->getCode() > 0 ?$exception->getCode():501);
                $message   = $exception->getMessage();
                $errorCode = $code;
                $error     = array('status'=>$status,'code'=>$code,'message'=>$message,'errorCode'=>$errorCode,'error'=>array('errorMessage'=>$message,'message'=>$message));
                return response($error,$code);
            break;
        }

        return parent::render($request, $exception);
    }
}
