<?php

namespace App\Http\Requests;

use Validator;

abstract class IRequest extends \Illuminate\Foundation\Http\FormRequest
{
    final protected function habitualBodyRules(): array
    {
        return [
            'id' => ['required', 'int'],
            'token' => ['required', 'string'],
        ];
    }

    /**
     * @return array|true fails
     */
    final public function validate(?array $rules = null, ?array $params = null): array|false
    {
        $validator = Validator::make(
            $rules ?? $this->all(),
            $params ?? $this->rules()
        )->stopOnFirstFailure(false);

        return $validator->fails() ? $validator->getMessageBag()->messages() : false;
    }

    abstract protected function rules(): array;

    abstract protected function store(): array;
}
