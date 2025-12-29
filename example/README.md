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
2. **Basic Success** - Hit `/api/demo/success`
3. **Pagination Magic** - Hit `/api/demo/users-paginated` (show auto meta!)
4. **Error Handling** - Hit `/api/demo/not-found` and `/api/demo/validate`
5. **Custom Data** - Hit `/api/demo/custom-data`

## Sample Responses

### Success Response
```json
{
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
