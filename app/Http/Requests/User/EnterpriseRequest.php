<?php

namespace App\Http\Requests\User;


class EnterpriseRequest extends UserRequest
{
  protected function store(): array{
    return array_merge(
      parent::store(),
      [
        'cnpj' => 'required|string',
        'company_name' => 'required|string'
      ]);
  }

  protected function update(): array{
    return array_merge(
      parent::update(),
      ['company_name' => 'nullable|string']
    );
  }
}
