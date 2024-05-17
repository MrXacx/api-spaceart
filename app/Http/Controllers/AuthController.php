<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\ResponseService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(protected ResponseService $responseService)
    {
    }

    public function authenticate(AuthRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        try {
            throw_unless(Auth::attempt($credentials), AuthenticationException::class);
            $user = $request->user();
            throw_unless($user->active, AuthenticationException::class);
            $user->tokens()->delete();
            $token = $user->createToken($request->device_name);

            return $this->responseService->sendMessage('User authenticated', ['token' => $token->plainTextToken]);
        } catch (AuthenticationException $e) {
            return $this->responseService->sendError('User not authenticated', ['Email or password is incorrect']);
        }
    }
}
