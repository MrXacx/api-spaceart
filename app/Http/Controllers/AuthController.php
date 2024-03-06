<?php

namespace App\Http\Controllers;

use App\Services\ResponseService;
use App\Http\Requests\AuthRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct(protected ResponseService $responseService)
    {
    }

    public function authenticate(AuthRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, $request->remember)) {
            $request->sanc()->regenerate();
            return $this->responseService->sendMessage('Authenticate worked', ['id' => auth()->user()->id]);
        } else {
            return $this->responseService->sendError('Authenticate failed');
        }
    }
}
