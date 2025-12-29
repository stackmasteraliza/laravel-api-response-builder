<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Stackmasteraliza\ApiResponse\Facades\ApiResponse;

/**
 * Demo Controller - Showcasing Laravel API Response Builder
 *
 * This controller demonstrates all the features of the package
 * Perfect for recording a demo video!
 */
class DemoController extends Controller
{
    /**
     * =============================================
     * SUCCESS RESPONSES
     * =============================================
     */

    /**
     * Basic success response
     * GET /api/demo/success
     */
    public function success(): JsonResponse
    {
        $data = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'admin'
        ];

        return ApiResponse::success($data, 'User retrieved successfully');
    }

    /**
     * Success with array of items
     * GET /api/demo/users
     */
    public function users(): JsonResponse
    {
        $users = User::all();

        return ApiResponse::success($users, 'Users retrieved successfully');
    }

    /**
     * Paginated response (auto-detected!)
     * GET /api/demo/users-paginated
     */
    public function usersPaginated(): JsonResponse
    {
        $users = User::paginate(5);

        return ApiResponse::success($users, 'Users retrieved with pagination');
    }

    /**
     * Created response (201)
     * POST /api/demo/users
     */
    public function store(Request $request): JsonResponse
    {
        // Simulating user creation
        $user = [
            'id' => 99,
            'name' => $request->input('name', 'New User'),
            'email' => $request->input('email', 'newuser@example.com'),
            'created_at' => now()->toISOString()
        ];

        return ApiResponse::created($user, 'User created successfully');
    }

    /**
     * No content response (204)
     * DELETE /api/demo/users/1
     */
    public function destroy(): JsonResponse
    {
        // Simulating deletion
        return ApiResponse::noContent();
    }

    /**
     * =============================================
     * ERROR RESPONSES
     * =============================================
     */

    /**
     * Bad request (400)
     * GET /api/demo/bad-request
     */
    public function badRequest(): JsonResponse
    {
        return ApiResponse::badRequest('Invalid request parameters');
    }

    /**
     * Unauthorized (401)
     * GET /api/demo/unauthorized
     */
    public function unauthorized(): JsonResponse
    {
        return ApiResponse::unauthorized('Please login to continue');
    }

    /**
     * Forbidden (403)
     * GET /api/demo/forbidden
     */
    public function forbidden(): JsonResponse
    {
        return ApiResponse::forbidden('You do not have permission to access this resource');
    }

    /**
     * Not found (404)
     * GET /api/demo/not-found
     */
    public function notFound(): JsonResponse
    {
        return ApiResponse::notFound('User not found');
    }

    /**
     * Validation error (422)
     * POST /api/demo/validate
     */
    public function validationError(): JsonResponse
    {
        $errors = [
            'email' => ['The email field is required.', 'The email must be valid.'],
            'password' => ['The password must be at least 8 characters.']
        ];

        return ApiResponse::validationError($errors, 'Validation failed');
    }

    /**
     * Too many requests (429)
     * GET /api/demo/rate-limited
     */
    public function rateLimited(): JsonResponse
    {
        return ApiResponse::tooManyRequests('Too many requests. Please try again later.', 60);
    }

    /**
     * Server error (500)
     * GET /api/demo/server-error
     */
    public function serverError(): JsonResponse
    {
        return ApiResponse::serverError('Something went wrong on our end');
    }

    /**
     * =============================================
     * ADVANCED FEATURES
     * =============================================
     */

    /**
     * Response with custom data fields
     * GET /api/demo/custom-data
     */
    public function customData(): JsonResponse
    {
        $user = [
            'id' => 1,
            'name' => 'John Doe'
        ];

        return ApiResponse::success($user, 'User with extra info')
            ->withData('permissions', ['read', 'write', 'delete'])
            ->withData('last_login', '2025-01-15 10:30:00');
    }

    /**
     * Response with custom headers
     * GET /api/demo/custom-headers
     */
    public function customHeaders(): JsonResponse
    {
        $data = ['status' => 'healthy'];

        return ApiResponse::success($data, 'API is healthy')
            ->withHeader('X-API-Version', '1.0.0')
            ->withHeader('X-Request-ID', uniqid());
    }

    /**
     * Accepted response for async operations (202)
     * POST /api/demo/async-job
     */
    public function asyncJob(): JsonResponse
    {
        $job = [
            'job_id' => 'job_' . uniqid(),
            'status' => 'processing',
            'check_status_url' => '/api/jobs/status/abc123'
        ];

        return ApiResponse::accepted($job, 'Your request is being processed');
    }

    /**
     * Conflict response (409)
     * POST /api/demo/conflict
     */
    public function conflict(): JsonResponse
    {
        return ApiResponse::conflict('This email is already registered');
    }
}
