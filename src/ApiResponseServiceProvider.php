<?php

namespace Stackmasteraliza\ApiResponse;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use Stackmasteraliza\ApiResponse\Console\GenerateApiDocsCommand;
use Stackmasteraliza\ApiResponse\Http\Controllers\SwaggerController;
use Stackmasteraliza\ApiResponse\OpenApi\OpenApiGenerator;
use Stackmasteraliza\ApiResponse\Testing\ApiResponseAssertions;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/api-response.php',
            'api-response'
        );

        $this->app->singleton('api-response', function ($app) {
            return new ApiResponse();
        });

        $this->app->singleton(OpenApiGenerator::class, function ($app) {
            return new OpenApiGenerator($app['router']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/api-response.php' => config_path('api-response.php'),
            ], 'api-response-config');

            $this->commands([
                GenerateApiDocsCommand::class,
            ]);

            $this->registerTestingMacros();
        }

        $this->registerOpenApiRoutes();
    }

    /**
     * Register the OpenAPI documentation routes.
     */
    protected function registerOpenApiRoutes(): void
    {
        if (! config('api-response.openapi.enabled', true)) {
            return;
        }

        $docsRoute = config('api-response.openapi.docs_route', 'api-docs');

        Route::get($docsRoute, [SwaggerController::class, 'index'])
            ->name('api-docs.index');

        Route::get($docsRoute . '/openapi.json', [SwaggerController::class, 'spec'])
            ->name('api-docs.spec');
    }

    /**
     * Register testing macros for TestResponse.
     */
    protected function registerTestingMacros(): void
    {
        $methods = [
            'assertApiSuccess',
            'assertApiError',
            'assertApiStatusCode',
            'assertApiMessage',
            'assertApiHasData',
            'assertApiDataCount',
            'assertApiPaginated',
            'assertApiCursorPaginated',
            'assertApiHasErrors',
            'assertApiData',
            'assertApiDataContains',
        ];

        foreach ($methods as $method) {
            if (! TestResponse::hasMacro($method)) {
                TestResponse::macro($method, function (...$args) use ($method) {
                    $trait = new class {
                        use ApiResponseAssertions;

                        public TestResponse $response;

                        public function json($key = null)
                        {
                            return $this->response->json($key);
                        }

                        public function assertJson($value, $strict = false)
                        {
                            $this->response->assertJson($value, $strict);
                            return $this;
                        }

                        public function assertStatus($status)
                        {
                            $this->response->assertStatus($status);
                            return $this;
                        }
                    };

                    $trait->response = $this;

                    return $trait->$method(...$args);
                });
            }
        }
    }
}
