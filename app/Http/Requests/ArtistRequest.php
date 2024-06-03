<?php

namespace App\Http\Requests;

use App\Enumerate\Art;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class ArtistRequest extends UserRequest
{
    /**
     * @OA\Schema(
     *      schema="ArtistStoreBody",
     *
     *       @OA\Property(property="cpf", type="string", pattern="^\d{11}$", example="01499146000196"),
     *       @OA\Property(property="birthday", type="date", format="d/m/Y", example="01/01/1970"),
     *       @OA\Property(property="art", enum="App\Enumerate\Art", example="music"),
     *       @OA\Property(property="wage", type="number", example="100"),
     *       required={
     *           "cpf",
     *           "birthday",
     *           "art",
     *           "wage",
     *       },
     *  )
     */
    protected function store(): array
    {
        return array_merge(
            parent::store(),
            [
                'cpf' => ['required', 'cpf'],
                'birthday' => ['required', 'date_format:d/m/Y'],
                'art' => ['required', Rule::enum(Art::class)],
                'wage' => ['required', 'decimal:0,2'],
            ]);
    }

    /**
     * @OA\Schema(
     *     schema="ArtistUpdateBody",
     *     @OA\Property(property="art", enum="App\Enumerate\Art", example="music"),
     *     @OA\Property(property="wage", type="number", example="100"),
     * )
     */
    protected function update(): array
    {
        return array_merge(
            parent::update(),
            [
                'art' => [Rule::enum(Art::class)],
                'wage' => 'decimal:0,2',
            ]);
    }
}
