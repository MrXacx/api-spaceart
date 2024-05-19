<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\Logger;
use App\Services\ResponseService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly ResponseService $responseService,
        private readonly Logger $logger,
    ) {
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
            $this->logger->auth('Has been authenticated.');

            return $this->responseService->sendMessage('User authenticated', ['token' => $token->plainTextToken]);
        } catch (AuthenticationException) {
            $this->logger->auth("Authentication failed with {$credentials['email']}.");

            return $this->responseService->sendError('User not authenticated.', ['Email or password is incorrect.']);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->logger->auth('Logged out.');
        $request->user()->tokens()->delete();

        return $this->responseService->sendMessage('User logout.');
    }
}
