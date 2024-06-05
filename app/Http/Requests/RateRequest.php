<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;

class RateRequest extends FormRequest
{
    public function rules(): array
    {
        return match ($this->method()) {
            'GET', 'DELETE' => [],
            'POST' => $this->store(),
            'PUT' => $this->update(),
        };
    }

    /**
     * @OA\RequestBody(
     *     request="RateStore",
     *
     *     @OA\JsonContent(
     *          required={"author_id", "score", "note"},
     *
     *         @OA\Property(property="author_id", type="int"),
     *         @OA\Property(property="score", type="number", minimum=0, maximum=5),
     *         @OA\Property(property="note", type="string"),
     *     )
     * )
     */
    private function store(): array
    {
        return [
            'author_id' => ['required', 'exists:users,id'],
            'score' => ['required', 'decimal:0,2', 'min:0', 'max:5'],
            'note' => ['required', 'string'],
        ];
    }

    /**
     * @OA\RequestBody(
     *     request="RateUpdate",
     *
     *     @OA\JsonContent(
     *         required={"score"},
     *         @OA\Property(property="score", type="number", minimum=0, maximum=5),
     *         @OA\Property(property="note", type="string"),
     *     )
     * )
     */
    private function update()
    {
        return [
            'score' => ['required', 'decimal:0,2', 'min:0', 'max:5'],
            'note' => ['required', 'string'],
        ];
    }
}
