<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RequestStore as Stores;

class RequestStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response =  $next($request);


        $reqStore = new Stores;

        $reqStore->request = $request;
        $reqStore->ip = $_SERVER["REMOTE_ADDR"]; 
        $reqStore->device = $_SERVER["REQUEST_METHOD"];
        $reqStore->endpoint = $_SERVER["REQUEST_URI"];
        $reqStore->response = $response;

        $reqStore->save();
        return $response;
    }
}
