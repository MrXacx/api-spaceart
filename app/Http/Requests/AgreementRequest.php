<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgreementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->getMethod()) {
            'GET', 'DELETE' => [],
            'POST' => $this->store(),
            'PUT' => $this->update(),
        };
    }

    private function store(): array
    {
        return [
            'enterprise_id' => ['required', 'exists:enterprises,id'],
            'artist_id' => ['required', 'exists:artists,id'],
            'price' => ['required', 'decimal:0,2'],
            'note' => ['required', 'string'],
            'date' => ['required', 'date_format:d/m/Y', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }

    private function update(): array
    {
        return [
            'note' => ['string'],
            'date' => ['date_format:d/m/Y', 'after_or_equal:today'],
            'start_time' => ['required_with:date', 'date_format:H:i'],
            'end_time' => ['required_with:date', 'date_format:H:i', 'after:start_time'],
            'status' => ['required', 'string'],
        ];
    }
}
