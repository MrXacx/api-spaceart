<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ResponseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

abstract class ISubController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(protected ResponseService $responseService)
    {
        $this->setSanctumMiddleware();
    }

    protected function setSanctumMiddleware()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display the all resources.
     *
     * @return Collection<Model>
     */
    abstract public function index(Request $request): JsonResponse|RedirectResponse;

    abstract protected function fetch(string $serviceId, string $userId): Model;
}