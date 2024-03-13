<?php

namespace App\Http\Requests;

class ArtistRequest extends UserRequest
{
    protected function store(): array
    {
        return array_merge(
            parent::store(),
            [
                'cpf' => ['required', 'cpf'],
                'birthday' => ['required', 'string'],
                'art' => ['required', 'string'],
                'wage' => ['required', 'numeric'],
            ]);
    }

    protected function update(): array
    {
        return array_merge(
            parent::update(),
            [
                'art' => 'string',
                'wage' => 'numeric',
            ]);
    }
}