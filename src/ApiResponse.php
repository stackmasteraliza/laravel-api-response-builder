<?php

namespace Stackmasteraliza\ApiResponse;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class ApiResponse
{
    use Macroable;
    protected array $response = [];
    protected int $statusCode = 200;
    protected array $headers = [];

    /**
     * Create a success response.
     */
    public function success(mixed $data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $this->statusCode = $statusCode;

        $this->response = [
            'success' => true,
            'message' => $message,
            'data' => $this->formatData($data),
        ];

        if ($data instanceof CursorPaginator) {
            $this->response['meta'] = $this->getCursorPaginationMeta($data);
        } elseif ($data instanceof AbstractPaginator) {
            $this->response['meta'] = $this->getPaginationMeta($data);
        }

        return $this->toResponse();
    }

    /**
     * Create an error response.
     */
    public function error(string $message = 'Error', int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        $this->statusCode = $statusCode;

        $this->response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $this->response['errors'] = $errors;
        }

        return $this->toResponse();
    }

    /**
     * Create a created response (201).
     */
    public function created(mixed $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Create a no content response (204).
     */
    public function noContent(): JsonResponse
    {
        $this->statusCode = 204;
        $this->response = [];

        return $this->toResponse();
    }

    /**
     * Create an accepted response (202).
     */
    public function accepted(mixed $data = null, string $message = 'Request accepted'): JsonResponse
    {
        return $this->success($data, $message, 202);
    }

    /**
     * Create a bad request response (400).
     */
    public function badRequest(string $message = 'Bad request', mixed $errors = null): JsonResponse
    {
        return $this->error($message, 400, $errors);
    }

    /**
     * Create an unauthorized response (401).
     */
    public function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, 401);
    }

    /**
     * Create a forbidden response (403).
     */
    public function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, 403);
    }

    /**
     * Create a not found response (404).
     */
    public function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * Create a method not allowed response (405).
     */
    public function methodNotAllowed(string $message = 'Method not allowed'): JsonResponse
    {
        return $this->error($message, 405);
    }

    /**
     * Create a conflict response (409).
     */
    public function conflict(string $message = 'Conflict', mixed $errors = null): JsonResponse
    {
        return $this->error($message, 409, $errors);
    }

    /**
     * Create an unprocessable entity response (422).
     */
    public function unprocessable(string $message = 'Validation failed', mixed $errors = null): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }

    /**
     * Create a validation error response (422).
     */
    public function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->unprocessable($message, $errors);
    }

    /**
     * Create a too many requests response (429).
     */
    public function tooManyRequests(string $message = 'Too many requests', ?int $retryAfter = null): JsonResponse
    {
        if ($retryAfter !== null) {
            $this->withHeader('Retry-After', $retryAfter);
        }

        return $this->error($message, 429);
    }

    /**
     * Create a server error response (500).
     */
    public function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return $this->error($message, 500);
    }

    /**
     * Create a service unavailable response (503).
     */
    public function serviceUnavailable(string $message = 'Service unavailable'): JsonResponse
    {
        return $this->error($message, 503);
    }

    /**
     * Add custom data to the response.
     */
    public function withData(string $key, mixed $value): self
    {
        $this->response[$key] = $value;

        return $this;
    }

    /**
     * Add headers to the response.
     */
    public function withHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Add multiple headers to the response.
     */
    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Format the data based on its type.
     */
    protected function formatData(mixed $data): mixed
    {
        if ($data instanceof JsonResource) {
            return $data->resolve();
        }

        if ($data instanceof ResourceCollection) {
            return $data->resolve();
        }

        if ($data instanceof CursorPaginator) {
            return $data->items();
        }

        if ($data instanceof AbstractPaginator) {
            return $data->items();
        }

        if ($data instanceof Collection) {
            return $data->toArray();
        }

        return $data;
    }

    /**
     * Get pagination metadata.
     */
    protected function getPaginationMeta(AbstractPaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'path' => $paginator->path(),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    /**
     * Get cursor pagination metadata.
     */
    protected function getCursorPaginationMeta(CursorPaginator $paginator): array
    {
        return [
            'per_page' => $paginator->perPage(),
            'next_cursor' => $paginator->nextCursor()?->encode(),
            'prev_cursor' => $paginator->previousCursor()?->encode(),
            'path' => $paginator->path(),
            'links' => [
                'next' => $paginator->nextPageUrl(),
                'prev' => $paginator->previousPageUrl(),
            ],
        ];
    }

    /**
     * Convert to JSON response.
     */
    protected function toResponse(): JsonResponse
    {
        $response = $this->response;

        if (config('api-response.include_status_code', true)) {
            $statusCodeKey = config('api-response.keys.status_code', 'status_code');
            $response = [$statusCodeKey => $this->statusCode] + $response;
        }

        return response()->json($response, $this->statusCode, $this->headers);
    }
}
