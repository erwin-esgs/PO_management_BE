<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    //protected function redirectTo($request)
	public function handle($request, Closure $next, ...$guards)
    {
        try {
            $decoded = JWTAuth::parseToken()->authenticate();
			$token = JWTAuth::getToken();
			$decoded = json_decode(base64_decode(explode(".", $token)[1]));
			$user = User::find($decoded->id);
			
			if(!$user){
				return abort(401);
			}
			if( $user->last_login > $decoded->iat ){
				return abort(401);
			}
			$user->last_login = time();
			if( !($user->save()) ){
				return abort(401);
			};
			return $next($request);
        } catch (\Throwable $th) {
            return abort(401);
        }
    }
}
