<?php

namespace App\Http\Requests\User;


class EnterpriseRequest extends UserRequest
{
  protected function store(): array{
    return array_merge(
      parent::store(),
      [
        'cnpj' => 'required|string',
        'companyName' => 'required|string'
      ]);
  }

  protected function update(): array{
    return array_merge(
      parent::update(),
      ['companyName' => 'nullable|string']
    );
  }
}
