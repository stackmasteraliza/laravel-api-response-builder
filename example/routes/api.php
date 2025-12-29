<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Demo
|--------------------------------------------------------------------------
|
| These routes demonstrate the Laravel API Response Builder package.
| Run: php artisan serve
| Then test endpoints with Postman or browser
|
*/

// ==========================================
// SUCCESS RESPONSES
// ==========================================

// Basic success - GET /api/demo/success
Route::get('/demo/success', [DemoController::class, 'success']);

// List all users - GET /api/demo/users
Route::get('/demo/users', [DemoController::class, 'users']);

// Paginated users - GET /api/demo/users-paginated
Route::get('/demo/users-paginated', [DemoController::class, 'usersPaginated']);

// Create user (201) - POST /api/demo/users
Route::post('/demo/users', [DemoController::class, 'store']);

// Delete user (204) - DELETE /api/demo/users/1
Route::delete('/demo/users/{id}', [DemoController::class, 'destroy']);

// ==========================================
// ERROR RESPONSES
// ==========================================

// Bad request (400) - GET /api/demo/bad-request
Route::get('/demo/bad-request', [DemoController::class, 'badRequest']);

// Unauthorized (401) - GET /api/demo/unauthorized
Route::get('/demo/unauthorized', [DemoController::class, 'unauthorized']);

// Forbidden (403) - GET /api/demo/forbidden
Route::get('/demo/forbidden', [DemoController::class, 'forbidden']);

// Not found (404) - GET /api/demo/not-found
Route::get('/demo/not-found', [DemoController::class, 'notFound']);

// Validation error (422) - POST /api/demo/validate
Route::post('/demo/validate', [DemoController::class, 'validationError']);

// Rate limited (429) - GET /api/demo/rate-limited
Route::get('/demo/rate-limited', [DemoController::class, 'rateLimited']);

// Server error (500) - GET /api/demo/server-error
Route::get('/demo/server-error', [DemoController::class, 'serverError']);

// ==========================================
// ADVANCED FEATURES
// ==========================================

// Custom data fields - GET /api/demo/custom-data
Route::get('/demo/custom-data', [DemoController::class, 'customData']);

// Custom headers - GET /api/demo/custom-headers
Route::get('/demo/custom-headers', [DemoController::class, 'customHeaders']);

// Async job (202) - POST /api/demo/async-job
Route::post('/demo/async-job', [DemoController::class, 'asyncJob']);

// Conflict (409) - POST /api/demo/conflict
Route::post('/demo/conflict', [DemoController::class, 'conflict']);
