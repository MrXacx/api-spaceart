<?php

namespace App\Http\Requests;

use OpenApi\Annotations as OA;

class EnterpriseRequest extends UserRequest
{
    /**
     * @OA\Schema(
     *      schema="EnterpriseStoreBody",
     *      required={
     *            "cnpj",
     *            "company_name",
     *            "address_complement",
     *        },
     *
     *      @OA\Property(property="cnpj", type="string", pattern="^\d{14}$", example="40033796599"),
     *      @OA\Property(property="company_name", type="string", minLength=3, example="José e Gabriela Esportes ME"),
     *      @OA\Property(property="address_complement", type="string", example="Beside of SESI Saúde"),
     *  )
     */
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

    /**
     * @OA\Schema(
     *      schema="EnterpriseUpdateBody",
     *
     *      @OA\Property(property="company_name", type="string", minLength=3, example="José e Gabriela Esportes ME"),
     *      @OA\Property(property="address_complement", type="string", example="Beside of SESI Saúde"),
     *  )
     */
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
