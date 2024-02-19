<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class RegexProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Validator::extend(
            'phone',
            fn ($attribute, $value) => preg_match('/^[1-9]\d9(8|9)\d{7}$/', $value),
            'phone must be a valid Brazilian phone number'
        );
        Validator::extend(
            'url',
            fn ($attribute, $value) => preg_match('/^https{0,1}://[\w\.-]+/(([\w\.-_]+)/)*(\?([\w_-]+=[\w%-]+&{0,1})+){0,1}$/', $value),
            'url is not valid'
        );
        Validator::extend(
            'cpf',
            fn ($attribute, $value) => preg_match('/^\d{11}$/', $value),
            'cpf must be a national id number for people in Brazil'
        );
        Validator::extend(
            'cnpj',
            fn ($attribute, $value) => preg_match('/^\d{14}$/', $value),
            'cnpj must be a national id number for enterprise in Brazil'
        );
        Validator::extend(
            'postal_code',
            fn ($attribute, $value) => preg_match('/\b\d{8}\b/', $value),
            'postal_code must be a CEP'
        );
    }
}
