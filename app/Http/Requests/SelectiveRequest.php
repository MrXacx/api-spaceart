<?php

namespace App\Http\Requests;

use Enumerate\Art;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SelectiveRequest extends FormRequest
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
            'enterprise_id' => ['required', 'exists:enterprises,id'],
            'title' => ['required', 'string', 'min:5', 'max:30'],
            'start_moment' => ['required', 'date_format:d/m/Y H:i', 'after:today'],
            'end_moment' => ['required', 'date_format:d/m/Y H:i', 'after:start_moment'],
            'art' => ['required', Rule::enum(Art::class)],
            'note' => ['required', 'string'],
            'price' => ['required', 'decimal:0,2'],
        ];
    }
    private function update()
    {
        return [
            'title' => ['string', 'min:5', 'max:30'],
            'start_moment' => ['date_format:d/m/Y H:i', 'after:today'],
            'end_moment' => ['required_with:start_moment', 'date_format:d/m/Y H:i', 'after:start_moment'],
            'note' => ['string'],
            'price' => ['decimal:0,2'],
        ];
    }
}
