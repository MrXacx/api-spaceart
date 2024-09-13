<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;

class AuthRequest extends FormRequest
{
    /**
     * @OA\RequestBody(
     *     request="Auth",
     *
     *     @OA\JsonContent(
     *          required={"email", "password", "device_name"},
     *
     *          @OA\Property(property="email", type="string"),
     *          @OA\Property(property="password", type="string", minLength=8),
     *          @OA\Property(property="device_name", type="string"),
     *     )
     * )
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'device_name' => ['required', 'string'],
        ];
    }
}
