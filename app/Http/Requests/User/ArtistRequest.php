<?php

namespace App\Http\Requests\User;

class ArtistRequest extends UserRequest
{
  protected function store(): array
  {
    return array_merge(
      parent::store(),
      [
        'cpf' => 'required|cpf',
        'birthday' => 'required|string',
        'art' => 'required|string',
        'wage' => 'required|numeric',
      ]);
  }

  protected function update(): array
  {
    return array_merge(
      parent::rules(),
      [
        'art' => 'nullable|string',
        'wage' => 'nullable|numeric',
      ]
    );
  }
}
