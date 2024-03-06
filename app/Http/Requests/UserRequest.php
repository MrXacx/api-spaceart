<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    protected function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule','array<mixed>','string]>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'GET' => ['id' => ['required', 'int']],
            'POST' => $this->store(),
            'PUT' => $this->update(),
            default => []
        };
    }

    protected function store(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:30'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'phone'],
            'image' => ['required', 'string'],
            'postal_code' => ['required', 'postal_code'],
            'type' => ['required', 'string'],
        ];
    }

    protected function update(): array
    {
        return [
            'name' => ['string', 'min:3', 'max:30'],
            'password' => ['string', 'min:8'],
            'phone' => ['phone'],
            'image' => ['string'],
            'postal_code' => ['postal_code'],
            'address' => ['string'],
        ];
    }
}
