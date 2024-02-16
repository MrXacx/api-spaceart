<?php

namespace App\Http\Requests\User;

abstract class UserRequest extends \App\Http\Requests\IRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'GET' => $this->habitualBodyRules(),
            'POST' => $this->store(),
            'PUT' => $this->update(),
            'DELETE' => $this->habitualBodyRules(),
        };
    }

    protected function store(): array
    {
        return [
            'name' => 'required|string|min:3|max:30',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string',
            'image' => 'required|string',
            'postal_code' => 'required|string',
            'address' => 'string',
            'type' => 'required|string'
        ];
    }

    protected function update(): array
    {
        return [
            'id' => 'required|int',
            'token' => 'required|string',
            'type' => 'required|string',
            'name' => 'nullable|string|min:3|max:30',
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string',
            'image' => 'nullable|string',
            'postalCode' => 'nullable|string',
            'address' => 'nullable|string',
        ];
    }
}