<?php

namespace App\Http\Requests;

use Enumerate\Art;
use Illuminate\Validation\Rule;

class ArtistRequest extends UserRequest
{
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
