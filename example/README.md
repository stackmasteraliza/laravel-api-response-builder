# Laravel API Response Builder - Demo Project

This example project demonstrates all features of the **Laravel API Response Builder** package.

## Quick Setup

### 1. Create a fresh Laravel project

```bash
composer create-project laravel/laravel demo-app
cd demo-app
```

### 2. Install the package

```bash
composer require stackmasteraliza/laravel-api-response
```

### 3. Copy demo files

Copy the following files from this `example` folder to your Laravel project:

- `app/Http/Controllers/DemoController.php`
- `app/Models/User.php` (replace existing)
- `routes/api.php` (replace existing)
- `database/migrations/2025_01_01_000000_create_users_table.php`
- `database/seeders/UserSeeder.php`

### 4. Run migrations and seed

```bash
php artisan migrate:fresh
php artisan db:seed --class=UserSeeder
```

### 5. Start the server

```bash
php artisan serve
```

### 6. View API Documentation

Visit `http://localhost:8000/api-docs` to see the auto-generated Swagger documentation!

---

## Auto-Generated Swagger Documentation

The package automatically generates OpenAPI/Swagger documentation from your routes. No additional coding required!

### View Documentation

Simply visit `/api-docs` in your browser to see the interactive Swagger UI.

### Generate Static File

```bash
php artisan api:docs
```

This creates `public/api-docs/openapi.json` that you can use with any OpenAPI-compatible tool.

### Using PHP Attributes (Optional)

For more detailed documentation, add attributes to your controller methods:

```php
use Stackmasteraliza\ApiResponse\Attributes\ApiEndpoint;
use Stackmasteraliza\ApiResponse\Attributes\ApiRequest;
use Stackmasteraliza\ApiResponse\Attributes\ApiRequestBody;
use Stackmasteraliza\ApiResponse\Attributes\ApiResponse;

#[ApiEndpoint(
    summary: 'Get a single user',
    description: 'Retrieve a single user by ID',
    tags: ['Users']
)]
#[ApiResponse(status: 200, description: 'User retrieved successfully')]
#[ApiResponse(status: 404, description: 'User not found', ref: 'ErrorResponse')]
public function show(int $id): JsonResponse
{
    // ...
}
```

---

## Demo Endpoints

### Success Responses

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/demo/success` | Basic success response |
| GET | `/api/demo/users` | List all users |
| GET | `/api/demo/users-paginated` | Paginated response with metadata |
| POST | `/api/demo/users` | Created response (201) |
| DELETE | `/api/demo/users/1` | No content response (204) |

### Error Responses

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/demo/bad-request` | Bad request (400) |
| GET | `/api/demo/unauthorized` | Unauthorized (401) |
| GET | `/api/demo/forbidden` | Forbidden (403) |
| GET | `/api/demo/not-found` | Not found (404) |
| POST | `/api/demo/validate` | Validation error (422) |
| GET | `/api/demo/rate-limited` | Too many requests (429) |
| GET | `/api/demo/server-error` | Server error (500) |

### Advanced Features

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/demo/custom-data` | Response with extra data fields |
| GET | `/api/demo/custom-headers` | Response with custom headers |
| POST | `/api/demo/async-job` | Accepted response (202) |
| POST | `/api/demo/conflict` | Conflict response (409) |

---

## Recording Tips

For your demo video, show these in order:

1. **Installation** - `composer require stackmasteraliza/laravel-api-response`
2. **Swagger Docs** - Visit `/api-docs` (auto-generated!)
3. **Basic Success** - Hit `/api/demo/success`
4. **Pagination Magic** - Hit `/api/demo/users-paginated` (show auto meta!)
5. **Error Handling** - Hit `/api/demo/not-found` and `/api/demo/validate`
6. **Custom Data** - Hit `/api/demo/custom-data`

## Sample Responses

### Success Response
```json
{
    "status_code": 200,
    "success": true,
    "message": "User retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "admin"
    }
}
```

### Paginated Response
```json
{
    "status_code": 200,
    "success": true,
    "message": "Users retrieved with pagination",
    "data": [...],
    "meta": {
        "current_page": 1,
        "per_page": 5,
        "total": 12,
        "last_page": 3,
        "from": 1,
        "to": 5
    }
}
```

### Validation Error Response
```json
{
    "status_code": 422,
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": [
            "The email field is required.",
            "The email must be valid."
        ],
        "password": [
            "The password must be at least 8 characters."
        ]
    }
}
```
