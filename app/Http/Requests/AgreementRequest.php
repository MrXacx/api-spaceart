<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;

class AgreementRequest extends FormRequest
{
    public function rules(): array
    {
        return match ($this->getMethod()) {
            'GET', 'DELETE' => [],
            'POST' => $this->store(),
            'PUT' => $this->update(),
        };
    }

    /**
     * @OA\RequestBody(
     *     request="AgreementStore",
     *
     *     @OA\JsonContent(
     *         required={"enterprise_id", "artist_id", "price", "note", "date","start_time","end_time",},
     *
     *         @OA\Property(property="enterprise_id", type="int"),
     *         @OA\Property(property="artist_id", type="int"),
     *         @OA\Property(property="price", type="number", format="^\d*(\.\d*)?$", example="350.99"),
     *         @OA\Property(property="note", type="string"),
     *         @OA\Property(property="date", type="date", format="d/m/Y", example="31/12/2005"),
     *         @OA\Property(property="start_time", type="date", format="H:i", example="00:00"),
     *         @OA\Property(property="end_time", type="date", format="H:i", example="22:00"),
     *     )
     * )
     */
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

    /**
     * @OA\RequestBody(
     *     request="AgreementUpdate",
     *
     *     @OA\JsonContent(
     *         required={"status"},
     *
     *         @OA\Property(property="note", type="string"),
     *         @OA\Property(property="date", type="date", format="d/m/Y", example="31/12/2005"),
     *         @OA\Property(property="start_time", type="date", format="H:i", example="00:00"),
     *         @OA\Property(property="end_time", type="date", format="H:i", example="22:00"),
     *         @OA\Property(property="status", type="enum", enum="App\Enumerate\AgreementStatus"),
     *     )
     * )
     */
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
