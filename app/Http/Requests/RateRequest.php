<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'GET', 'DELETE' => [],
            'POST' => $this->store(),
            'PUT' => $this->update(),
        };
    }

    private function store()
    {
        return [
            'author_id' => ['required', 'exists:users,id'],
            'score' => ['required', 'decimal:0,2', 'min:0', 'max:5'],
            'note' => ['required', 'string'],
        ];
    }

    private function update()
    {
        return [
            'score' => ['required', 'decimal:0,2', 'min:0', 'max:5'],
            'note' => ['required', 'string'],
        ];
    }
}
