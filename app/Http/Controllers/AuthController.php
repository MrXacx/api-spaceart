<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\ResponseService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(protected ResponseService $responseService)
    {
    }

    public function authenticate(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = $request->user();

            $user->tokens()->delete();
            $token = $user->createToken($request->device_name);

            return $this->responseService->sendMessage('User authenticated', ['token' => $token->plainTextToken]);
        } else {
            return $this->responseService->sendError('User not authenticated');
        }
    }
}
