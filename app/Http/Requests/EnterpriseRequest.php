<?php

namespace App\Http\Requests;

class EnterpriseRequest extends UserRequest
{
    protected function store(): array
    {
        return array_merge(
            parent::store(),
            [
                'cnpj' => ['required', 'cnpj'],
                'company_name' => ['required', 'string', 'min:3'],
                'address_complement' => ['required', 'string'],
            ]
        );
    }

    protected function update(): array
    {
        return array_merge(
            parent::update(),
            [
                'company_name' => ['string', 'min:3'],
                'address_complement' => ['string'],
            ]
        );
    }
}
