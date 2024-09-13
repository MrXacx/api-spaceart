<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;

class SelectiveCandidateRequest extends FormRequest
{
    /**
     * @OA\RequestBody(
     *     request="SelectiveCandidateStore",
     *
     *     @OA\JsonContent(required={"artist_id"}, @OA\Property(property="artist_id", type="int"))
     * )
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'POST' => ['artist_id' => ['required', 'exists:artists,id']],
        };
    }
}
