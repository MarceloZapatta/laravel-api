<?php

namespace App\Http\Controllers;

use App\Service\Authentication;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticationController
{
    /**
     * @var Authentication
     */
    private $authenticationService;

    public function __construct(Authentication $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * Register a new user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $registred = $this->authenticationService->register($request);

            if (!$registred) {
                return response()->json(['message' => 'Error when trying to register the user.'], 500);
            }

            return response()->json(['message' => 'User registred.'], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error ocurred.'
            ], 500);
        }
    }

    /**
     * Return a JWT token
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $token = $this->authenticationService->login($request);

            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->respondWithToken($token);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error ocurred.'
            ], 500);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
