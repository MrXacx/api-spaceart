<?php

declare(strict_types=1);

namespace App\Http\Controllers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


abstract class IController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Display the all resources.
     * @return Collection<Model>
     */
    protected abstract function list(): Collection|JsonResponse;

    /**
     * Display the specified resource.
     */
   protected abstract function show(Request $request): Model|JsonResponse;

    /**
     * Store a newly created resource in storage.
     */
   protected abstract function store(FormRequest $request): Model|JsonResponse;

    /**
     * Update the specified resource in storage.
     */
    protected abstract function update(FormRequest $request): Model|JsonResponse;

    /**
     * Remove the specified resource from storage.
     */
    protected abstract function destroy(FormRequest $request): JsonResponse;
}
