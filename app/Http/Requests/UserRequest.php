<?php

namespace App\Http\Requests;

use Enumerate\Account;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule,array<mixed>,string
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'GET','DELETE' => [],
            'POST' => $this->store(),
            'PUT' => $this->update()
        };
    }

    protected function store(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:30'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'phone'],
            'image' => ['required', 'string', 'max:10000000'],
            'postal_code' => ['required', 'postal_code'],
            'type' => ['required',  Rule::enum(Account::class)],
        ];
    }

    protected function update(): array
    {
        return [
            'type' => ['required', Rule::enum(Account::class)],
            'name' => ['string', 'min:3', 'max:30'],
            'password' => ['string', 'min:8'],
            'phone' => ['phone'],
            'image' => ['string', 'max:10000000'],
            'postal_code' => ['postal_code'],
            'address' => ['string'],
            'slug' => ['url'],
            'biography' => ['string', 'min:3', 'max:256'],
        ];
    }
}
