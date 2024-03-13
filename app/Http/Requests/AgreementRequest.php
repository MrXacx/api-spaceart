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
            'GET','DELETE' => ['id' => ['required', 'numeric']],
            'POST' => $this->store(),
            'PUT' => $this->update(),
        };
    }

    private function store(): array
    {
        return [
            'enterprise_id' => ['required', 'numeric'],
            'artist_id' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'note' => ['required', 'string'],
            'date' => ['required', 'string'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
        ];
    }

    private function update(): array
    {
        return [
            'note' => ['string'],
            'date' => ['string'],
            'start_time' => ['string'],
            'end_time' => ['string'],
            'status' => ['required', 'string'],
        ];
    }
}
