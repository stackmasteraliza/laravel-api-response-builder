<?php

namespace Stackmasteraliza\ApiResponse\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\JsonResponse success(mixed $data = null, string $message = 'Success', int $statusCode = 200)
 * @method static \Illuminate\Http\JsonResponse error(string $message = 'Error', int $statusCode = 400, mixed $errors = null)
 * @method static \Illuminate\Http\JsonResponse created(mixed $data = null, string $message = 'Resource created successfully')
 * @method static \Illuminate\Http\JsonResponse noContent()
 * @method static \Illuminate\Http\JsonResponse accepted(mixed $data = null, string $message = 'Request accepted')
 * @method static \Illuminate\Http\JsonResponse badRequest(string $message = 'Bad request', mixed $errors = null)
 * @method static \Illuminate\Http\JsonResponse unauthorized(string $message = 'Unauthorized')
 * @method static \Illuminate\Http\JsonResponse forbidden(string $message = 'Forbidden')
 * @method static \Illuminate\Http\JsonResponse notFound(string $message = 'Resource not found')
 * @method static \Illuminate\Http\JsonResponse methodNotAllowed(string $message = 'Method not allowed')
 * @method static \Illuminate\Http\JsonResponse conflict(string $message = 'Conflict', mixed $errors = null)
 * @method static \Illuminate\Http\JsonResponse unprocessable(string $message = 'Validation failed', mixed $errors = null)
 * @method static \Illuminate\Http\JsonResponse validationError(array $errors, string $message = 'Validation failed')
 * @method static \Illuminate\Http\JsonResponse tooManyRequests(string $message = 'Too many requests', ?int $retryAfter = null)
 * @method static \Illuminate\Http\JsonResponse serverError(string $message = 'Internal server error')
 * @method static \Illuminate\Http\JsonResponse serviceUnavailable(string $message = 'Service unavailable')
 * @method static \Stackmasteraliza\ApiResponse\ApiResponse withData(string $key, mixed $value)
 * @method static \Stackmasteraliza\ApiResponse\ApiResponse withHeader(string $key, string $value)
 * @method static \Stackmasteraliza\ApiResponse\ApiResponse withHeaders(array $headers)
 * @method static void macro(string $name, callable $macro)
 * @method static bool hasMacro(string $name)
 *
 * @see \Stackmasteraliza\ApiResponse\ApiResponse
 */
class ApiResponse extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'api-response';
    }
}
