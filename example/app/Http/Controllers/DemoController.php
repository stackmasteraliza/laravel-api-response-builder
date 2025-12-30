<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Stackmasteraliza\ApiResponse\Facades\ApiResponse;
use Stackmasteraliza\ApiResponse\Attributes\ApiEndpoint;
use Stackmasteraliza\ApiResponse\Attributes\ApiRequest;
use Stackmasteraliza\ApiResponse\Attributes\ApiRequestBody;
use Stackmasteraliza\ApiResponse\Attributes\ApiResponse as ApiResponseAttr;

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
    #[ApiEndpoint(
        summary: 'Get a single user',
        description: 'Retrieve a single user object with basic success response',
        tags: ['Users']
    )]
    #[ApiResponseAttr(status: 200, description: 'User retrieved successfully', example: [
        'status_code' => 200,
        'success' => true,
        'message' => 'User retrieved successfully',
        'data' => ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'admin']
    ])]
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
    #[ApiEndpoint(
        summary: 'List all users',
        description: 'Retrieve a list of all users in the system',
        tags: ['Users']
    )]
    #[ApiResponseAttr(status: 200, description: 'Users retrieved successfully', ref: 'SuccessResponse')]
    public function users(): JsonResponse
    {
        $users = User::all();

        return ApiResponse::success($users, 'Users retrieved successfully');
    }

    /**
     * Paginated response (auto-detected!)
     * GET /api/demo/users-paginated
     */
    #[ApiEndpoint(
        summary: 'List users with pagination',
        description: 'Retrieve a paginated list of users. Pagination metadata is automatically included.',
        tags: ['Users']
    )]
    #[ApiRequest(name: 'page', type: 'integer', in: 'query', description: 'Page number', example: 1)]
    #[ApiRequest(name: 'per_page', type: 'integer', in: 'query', description: 'Items per page', example: 5)]
    #[ApiResponseAttr(status: 200, description: 'Paginated users list', ref: 'PaginatedResponse')]
    public function usersPaginated(): JsonResponse
    {
        $users = User::paginate(5);

        return ApiResponse::success($users, 'Users retrieved with pagination');
    }

    /**
     * Created response (201)
     * POST /api/demo/users
     */
    #[ApiEndpoint(
        summary: 'Create a new user',
        description: 'Create a new user in the system. Returns the created user with 201 status.',
        tags: ['Users']
    )]
    #[ApiRequestBody(
        properties: ['name' => 'string', 'email' => 'string', 'password' => 'string'],
        required: ['name', 'email', 'password'],
        description: 'User creation data',
        example: ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'secret123']
    )]
    #[ApiResponseAttr(status: 201, description: 'User created successfully', example: [
        'status_code' => 201,
        'success' => true,
        'message' => 'User created successfully',
        'data' => ['id' => 99, 'name' => 'New User', 'email' => 'newuser@example.com']
    ])]
    #[ApiResponseAttr(status: 422, description: 'Validation error', ref: 'ValidationErrorResponse')]
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
    #[ApiEndpoint(
        summary: 'Delete a user',
        description: 'Delete a user from the system. Returns 204 No Content on success.',
        tags: ['Users']
    )]
    #[ApiResponseAttr(status: 204, description: 'User deleted successfully')]
    #[ApiResponseAttr(status: 404, description: 'User not found', ref: 'ErrorResponse')]
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
    #[ApiEndpoint(
        summary: 'Bad request example',
        description: 'Example of a 400 Bad Request response',
        tags: ['Errors']
    )]
    #[ApiResponseAttr(status: 400, description: 'Bad request', example: [
        'status_code' => 400,
        'success' => false,
        'message' => 'Invalid request parameters'
    ])]
    public function badRequest(): JsonResponse
    {
        return ApiResponse::badRequest('Invalid request parameters');
    }

    /**
     * Unauthorized (401)
     * GET /api/demo/unauthorized
     */
    #[ApiEndpoint(
        summary: 'Unauthorized example',
        description: 'Example of a 401 Unauthorized response',
        tags: ['Errors']
    )]
    #[ApiResponseAttr(status: 401, description: 'Unauthorized', example: [
        'status_code' => 401,
        'success' => false,
        'message' => 'Please login to continue'
    ])]
    public function unauthorized(): JsonResponse
    {
        return ApiResponse::unauthorized('Please login to continue');
    }

    /**
     * Forbidden (403)
     * GET /api/demo/forbidden
     */
    #[ApiEndpoint(
        summary: 'Forbidden example',
        description: 'Example of a 403 Forbidden response',
        tags: ['Errors']
    )]
    #[ApiResponseAttr(status: 403, description: 'Forbidden', example: [
        'status_code' => 403,
        'success' => false,
        'message' => 'You do not have permission to access this resource'
    ])]
    public function forbidden(): JsonResponse
    {
        return ApiResponse::forbidden('You do not have permission to access this resource');
    }

    /**
     * Not found (404)
     * GET /api/demo/not-found
     */
    #[ApiEndpoint(
        summary: 'Not found example',
        description: 'Example of a 404 Not Found response',
        tags: ['Errors']
    )]
    #[ApiResponseAttr(status: 404, description: 'Not found', example: [
        'status_code' => 404,
        'success' => false,
        'message' => 'User not found'
    ])]
    public function notFound(): JsonResponse
    {
        return ApiResponse::notFound('User not found');
    }

    /**
     * Validation error (422)
     * POST /api/demo/validate
     */
    #[ApiEndpoint(
        summary: 'Validation error example',
        description: 'Example of a 422 Validation Error response with multiple field errors',
        tags: ['Errors']
    )]
    #[ApiRequestBody(
        properties: ['email' => 'string', 'password' => 'string'],
        required: ['email', 'password'],
        description: 'Data to validate'
    )]
    #[ApiResponseAttr(status: 422, description: 'Validation failed', example: [
        'status_code' => 422,
        'success' => false,
        'message' => 'Validation failed',
        'errors' => [
            'email' => ['The email field is required.', 'The email must be valid.'],
            'password' => ['The password must be at least 8 characters.']
        ]
    ])]
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
    #[ApiEndpoint(
        summary: 'Rate limited example',
        description: 'Example of a 429 Too Many Requests response with Retry-After header',
        tags: ['Errors']
    )]
    #[ApiResponseAttr(status: 429, description: 'Too many requests', example: [
        'status_code' => 429,
        'success' => false,
        'message' => 'Too many requests. Please try again later.'
    ])]
    public function rateLimited(): JsonResponse
    {
        return ApiResponse::tooManyRequests('Too many requests. Please try again later.', 60);
    }

    /**
     * Server error (500)
     * GET /api/demo/server-error
     */
    #[ApiEndpoint(
        summary: 'Server error example',
        description: 'Example of a 500 Internal Server Error response',
        tags: ['Errors']
    )]
    #[ApiResponseAttr(status: 500, description: 'Server error', example: [
        'status_code' => 500,
        'success' => false,
        'message' => 'Something went wrong on our end'
    ])]
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
    #[ApiEndpoint(
        summary: 'Response with custom data',
        description: 'Demonstrates adding extra fields to the response using withData()',
        tags: ['Advanced']
    )]
    #[ApiResponseAttr(status: 200, description: 'User with extra info', example: [
        'status_code' => 200,
        'success' => true,
        'message' => 'User with extra info',
        'data' => ['id' => 1, 'name' => 'John Doe'],
        'permissions' => ['read', 'write', 'delete'],
        'last_login' => '2025-01-15 10:30:00'
    ])]
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
    #[ApiEndpoint(
        summary: 'Response with custom headers',
        description: 'Demonstrates adding custom HTTP headers to the response',
        tags: ['Advanced']
    )]
    #[ApiResponseAttr(status: 200, description: 'API health status with custom headers', example: [
        'status_code' => 200,
        'success' => true,
        'message' => 'API is healthy',
        'data' => ['status' => 'healthy']
    ])]
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
    #[ApiEndpoint(
        summary: 'Submit async job',
        description: 'Submit a job for async processing. Returns 202 Accepted with job details.',
        tags: ['Advanced']
    )]
    #[ApiRequestBody(
        properties: ['task' => 'string', 'priority' => 'string'],
        description: 'Job configuration'
    )]
    #[ApiResponseAttr(status: 202, description: 'Job accepted for processing', example: [
        'status_code' => 202,
        'success' => true,
        'message' => 'Your request is being processed',
        'data' => [
            'job_id' => 'job_abc123',
            'status' => 'processing',
            'check_status_url' => '/api/jobs/status/abc123'
        ]
    ])]
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
    #[ApiEndpoint(
        summary: 'Conflict example',
        description: 'Example of a 409 Conflict response when resource already exists',
        tags: ['Errors']
    )]
    #[ApiRequestBody(
        properties: ['email' => 'string'],
        description: 'Email to register'
    )]
    #[ApiResponseAttr(status: 409, description: 'Conflict', example: [
        'status_code' => 409,
        'success' => false,
        'message' => 'This email is already registered'
    ])]
    public function conflict(): JsonResponse
    {
        return ApiResponse::conflict('This email is already registered');
    }
}
