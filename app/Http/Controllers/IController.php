<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ResponseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class IController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function __construct(protected ResponseService $responseService){}

    /**
     * Display the all resources.
     *
     * @return Collection<Model>
     */
    abstract protected function list(): Collection|JsonResponse;

    /**
     * Display the specified resource.
     */
    abstract protected function show(Request $request): Model|JsonResponse;

    /**
     * Store a newly created resource in storage.
     */
    abstract protected function store(FormRequest $request): Model|JsonResponse;

    /**
     * Update the specified resource in storage.
     */
    abstract protected function update(FormRequest $request): Model|JsonResponse;

    /**
     * Remove the specified resource from storage.
     */
    abstract protected function destroy(FormRequest $request): JsonResponse;
}
