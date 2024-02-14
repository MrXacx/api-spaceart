<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

abstract class CreateEnterpriseRequest extends CreateUserRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return array_merge(
      parent::rules(),
      [
        'cnpj' => 'required|string',
        'companyName' => 'required|string'
      ]);
  }
}
