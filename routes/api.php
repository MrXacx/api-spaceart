<?php

declare(strict_types=1);

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\SelectiveCandidateController;
use App\Http\Controllers\SelectiveController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Create route of alternative access to PUT or DELETE route
 *
 * @throws Exception If the method is not PUT or DELETE
 */
function buildAltRoute(string $method, string $name): void
{
    $method = strtoupper($method); // Transform string to UPPERCASE
    $action = $method === 'PUT' ? 'update' : ($method === 'DELETE' ? 'delete' : throw new Exception);
    Route::post(
        "/{id}/$action",
        fn (Request $request) => redirect()->route("$name.$action", $request->all()))
        ->name($action);
}

/**
 * Build Api Resources
 */
function buildApiRoutes(array $apiResources): void
{
    foreach ($apiResources as $route => $resource) {
        $name = Str::afterLast($route, '/');

        [0 => $controller, 'except' => $except] = is_array($resource) ? $resource : [$resource, 'except' => []];

        Route::prefix("/$route")
            ->name("$name.alt.")
            ->group(function () use ($name, $except) {
                if (! array_search('update', $except)) {
                    buildAltRoute('PUT', $name);
                }
                if (! array_search('destroy', $except)) {
                    buildAltRoute('DELETE', $name);
                }
            });

        Route::apiResource("/$route", $controller, [
            'except' => $except,
            'parameters' => [$name => $resource['pathParameterName'] ?? 'id'],
        ]);
    }
}

$apiResources = [
    'user' => UserController::class,
    'agreement' => AgreementController::class,
    'selective' => SelectiveController::class,
    'post' => PostController::class,
    'agreement/{agreement}/rate' => [
        RateController::class,
        'except' => ['index'],
        'pathParameterName' => 'author',
    ],
];
buildApiRoutes($apiResources);

// Auth routes
Route::post('/auth', [AuthController::class, 'authenticate'])->name('auth');
Route::middleware('auth:sanctum')->post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Unique posting route
Route::post('/selective/{selective}/candidate', [SelectiveCandidateController::class, 'store']);
