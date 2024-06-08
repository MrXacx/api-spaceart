<?php

namespace App\Http\Requests;

use App\Enumerate\Art;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

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

    /**
     * @OA\RequestBody(
     *     request="SelectiveStore",
     *
     *     @OA\JsonContent(
     *         required={"enterprise_id","title", "start_moment", "end_moment", "art", "note", "price"},
     *         @OA\Property(property="enterprise_id", type="int"),
     *         @OA\Property(property="title", type="string", example="Summer 2025"),
     *         @OA\Property(property="start_moment", type="date", example="", format="d/m/Y H:i"),
     *         @OA\Property(property="end_moment", type="date", example="", format="d/m/Y H:i"),
     *         @OA\Property(property="art", type="enum", enum="App\Enumerate\Art"),
     *         @OA\Property(property="note", type="string", example="perfect!!"),
     *         @OA\Property(property="price", type="number"),
     *     )
     * )
     */
    private function store(): array
    {
        return [
            'enterprise_id' => ['required', 'exists:enterprises,id'],
            'title' => ['required', 'string', 'min:5', 'max:30'],
            'start_moment' => ['required', 'date_format:d/m/Y H:i', 'after:now'],
            'end_moment' => ['required', 'date_format:d/m/Y H:i', 'after:start_moment'],
            'art' => ['required', Rule::enum(Art::class)],
            'note' => ['required', 'string'],
            'price' => ['required', 'decimal:0,2'],
        ];
    }

    /**
     * @OA\RequestBody(
     *     request="SelectiveUpdate",
     *
     *     @OA\JsonContent(
     *         @OA\Property(property="title", type="string", example="Summer 2025"),
     *         @OA\Property(property="start_moment", type="date", example="", format="d/m/Y H:i"),
     *         @OA\Property(property="end_moment", type="date", example="", format="d/m/Y H:i"),
     *         @OA\Property(property="note", type="string", example="perfect!!"),
     *         @OA\Property(property="price", type="number"),
     *     )
     * )
     */
    private function update(): array
    {
        return [
            'title' => ['string', 'min:5', 'max:30'],
            'start_moment' => ['date_format:d/m/Y H:i', 'after:now'],
            'end_moment' => ['required_with:start_moment', 'date_format:d/m/Y H:i', 'after:start_moment'],
            'note' => ['string'],
            'price' => ['decimal:0,2'],
        ];
    }
}
