<?php

namespace App\Http\Requests;

use App\Enumerate\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        return match ($this->method()) {
            'GET', 'DELETE' => [],
            'POST' => $this->store(),
            'PUT' => $this->update()
        };
    }

    /**
     * @OA\RequestBody(
     *     request="UserStore",
     *
     *     @OA\JsonContent(
     *          type="object",
     *          required={"name", "email", "phone", "password", "type", "postal_code", "image"},
     *          oneOf={
     *              @OA\Property(ref="#/components/schemas/ArtistStoreBody"),
     *              @OA\Property(ref="#/components/schemas/EnterpriseStoreBody"),
     *          },
     *          @OA\Property(property="name", type="string", minLength=3, maxLength=30, example="José Carlos"),
     *          @OA\Property(property="email", type="string", example="example@org.net"),
     *          @OA\Property(property="phone", type="string", example="71988469787"),
     *          @OA\Property(property="password", type="string", minLength=8, example="<FO<k2&K83.;<RAeiC?@" ),
     *          @OA\Property(property="type", type="enum", enum="App\Enumerate\Account", type="enterprise"),
     *          @OA\Property(property="postal_code", type="string", example="41000000"),
     *          @OA\Property(property="image", type="string", maxLength=10000000),
     *     )
     * )
     */
    protected function store(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:30'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'phone'],
            'image' => ['required', 'string', 'max:10000000'],
            'postal_code' => ['required', 'postal_code'],
            'type' => ['required', Rule::enum(Account::class)],
        ];
    }

    /**
     * @OA\RequestBody(
     *     request="UserUpdate",
     *
     *     @OA\JsonContent(
     *          type="object",
     *          oneOf={
     *
     *               @OA\Property(ref="#/components/schemas/ArtistUpdateBody"),
     *               @OA\Property(ref="#/components/schemas/EnterpriseUpdateBody"),
     *           },
     *          @OA\Property(property="name", type="string", minLength=3, maxLength=30),
     *          @OA\Property(property="email", type="string"),
     *          @OA\Property(property="phone", type="string", minLength=11, maxLength=11),
     *          @OA\Property(property="password", type="string", minLength=8),
     *          @OA\Property(property="image", type="string", maxLength=10000000),
     *          @OA\Property(property="postal_code", type="string"),
     *          @OA\Property(property="address", type="string"),
     *          @OA\Property(property="slug", type="string", description="uri"),
     *     )
     * )
     */
    protected function update(): array
    {
        return [
            'type' => ['required', Rule::enum(Account::class)],
            'name' => ['string', 'min:3', 'max:30'],
            'password' => ['string', 'min:8'],
            'phone' => ['phone'],
            'image' => ['string', 'max:10000000'],
            'postal_code' => ['postal_code'],
            'address' => ['string'],
            'slug' => ['url'],
            'biography' => ['string', 'min:3', 'max:256'],
        ];
    }
}
