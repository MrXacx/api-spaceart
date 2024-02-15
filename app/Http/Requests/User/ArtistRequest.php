<?php

namespace App\Http\Requests\User;

class ArtistRequest extends UserRequest
{
  protected function store(): array
  {
    return array_merge(
      parent::store(),
      [
        'cpf' => 'required|string',
        'birthday' => 'required|string',
        'art' => 'required|string',
      ]);
  }

  protected function update(): array
  {
    return array_merge(
      parent::rules(),
      ['art' => 'nullable|string']
    );
  }
}
