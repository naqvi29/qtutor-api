<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use App\Helpers\PlatformAuthService;
use App\Helpers\helpers;
use Exception;
use \Illuminate\Database\QueryException;

class AuthorizationHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    private $mdlClient;
    public  $errorMessage = ['status'=>'failed','code'=>403,'message'=>'Access Denied'];

    public function __construct(){

    }

    // v1.0 - Default middleware function - Autoload based on middleware call
    public function handle(Request $request, Closure $next){
        if ($this->hasTooManyRequests()) {
            // sleep($this->limiter()->availableIn($this->throttleKey()) + 1);
            throw new Exception("Request has been forbidden", 403);
            return $this->handle();
        }
        //MA - Default Time is 60 Seconds
        $this->limiter()->hit($this->throttleKey(),  env("THROTTLETIME", "60"));

        $authService =  new PlatformAuthService($request);

        if($authService->validPlatform == true){
            $data = [ 'client' =>$authService->clientInfo ];
            $request['ClientInfo'] = $data;
        }else{
            throw new Exception("Invalid Header Key", 401);
        }

        return $next($request);
    }

    protected function hasTooManyRequests()
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey(), env("THROTTLEHIT", "100") // <= max attempts per minute
        );
    }
    protected function limiter()
    {
        return app(RateLimiter::class);
    }
    protected function throttleKey()
    {
        return 'custom_api_request';
    }
}
