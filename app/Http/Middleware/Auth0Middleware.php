<?php

namespace App\Http\Middleware;

use Closure;
use Auth0\SDK\JWTVerifier;
use App\Library\LazyAPI\Error;
use App\Library\LazyAPI\Response;

class Auth0Middleware
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader("Authorization")) {
            return self::errorResponse("ATH", 401, "Authorization Header not found");
        }

        $token = $request->bearerToken();

        if ($request->header("Authorization") == null || $token == null) {
            return self::errorResponse("ATH", 401, "No token provided");
        }

        try {
            $this->retrieveAndValidateToken($token);
        } catch (\Auth0\SDK\Exception\InvalidTokenException $e) {
            return self::errorResponse("ATH", 422, "Invalid Token");
        } catch (\Auth0\SDK\Exception\CoreException $e) {
            return self::errorResponse("ASV", 500, "Server Error");
        }

        return $next($request);
    }

    public function retrieveAndValidateToken($token)
    {
        try {
            $verifier = new JWTVerifier([
                "supported_algs" => ["RS256"],
                "valid_audiences" => ["https://demo_toptal.app/api/"],
                "authorized_iss" => ["https://ogbizi.auth0.com/"],
            ]);

            $decoded = $verifier->verifyAndDecode($token);
        } catch (\Auth0\SDK\Exception\CoreException $e) {
            throw $e;
        }
    }

    private static function errorResponse($code, $status, $message)
    {
        return response()->json(Response::new()->addError(new Error($code, $status, $message))->build(), $status);
    }
}
