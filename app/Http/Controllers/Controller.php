<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Display the all resources.
     */
    protected abstract function list(): Collection;

    /**
     * Display the specified resource.
     */
    protected abstract function show(Request $request): Model;

    /**
     * Store a newly created resource in storage.
     */
    protected abstract function store(Request $request): Model;

    /**
     * Update the specified resource in storage.
     */
    protected abstract function update(Request $request): Model;

    /**
     * Remove the specified resource from storage.
     */
    protected abstract function destroy(Request $request): void;
}
