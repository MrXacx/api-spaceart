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
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    public function __construct(
        private readonly ResponseService $responseService,
        private readonly Logger $logger,
    ) {
    }

    /**
     * @OA\Post(
     *     tags={"Auth"},
     *     path="/auth",
     *
     *     @OA\RequestBody(ref="#/components/requestBodies/Auth"),
     *
     *     @OA\Response(
     *          response="200",
     *          description="Has been authenticated",
     *
     *          @OA\JsonContent(
     *
     *           @OA\Property(property="data", type="object", @OA\Property(property="token", type="string"))
     *          )
     *    ),
     *
     *     @OA\Response(
     *          response="422",
     *          description="Authentication failed",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", default="User not authenticated."),
     *              @OA\Property(property="data", type="array", @OA\Items(minItems=1, @OA\Property(default="Email or password is incorrect."))),
     *              @OA\Property(property="fails", type="bool", default="true"),
     *          )
     *     )
     * )
     */
    public function authenticate(AuthRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        try {
            throw_unless(Auth::attempt($credentials), AuthenticationException::class);
            $user = $request->user();
            $user->tokens()->delete();
            $token = $user->createToken($request->device_name);
            $this->logger->auth('Has been authenticated.');

            return $this->responseService->sendMessage('User authenticated', ['token' => $token->plainTextToken]);
        } catch (AuthenticationException) {
            $this->logger->auth("Authentication failed with {$credentials['email']}.");

            return $this->responseService->sendError('User not authenticated.', ['Email or password is incorrect.']);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Auth"},
     *     path="/auth/logout",
     *     security={@OA\SecurityScheme(ref="#/components/securitySchemes/Sanctum")},
     *
     *     @OA\Response(response="200", description="Logged out", @OA\JsonContent(@OA\Property(property="message", type="string", default="User logout."))),
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $this->logger->auth('Logged out.');
        $request->user()->tokens()->delete();

        return $this->responseService->sendMessage('User logout.');
    }
}
