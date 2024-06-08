<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;

class PostRequest extends FormRequest
{
    /**
     * @OA\RequestBody(
     *     request="PostStore",
     *
     *     @OA\JsonContent(
     *
     *         @OA\Property(property="user_id", type="int"),
     *         @OA\Property(property="text", type="string"),
     *         @OA\Property(property="image", description="Image in base64 ou URL", type="string", maxLength=10000000),
     *     )
     * )
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'GET', 'DELETE' => [],
            'POST' => [
                'user_id' => ['exists:users,id'],
                'text' => ['string', 'required'],
                'image' => ['string', 'max:10000000', 'required'],
            ],
        };
    }
}
