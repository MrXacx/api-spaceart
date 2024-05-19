<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
