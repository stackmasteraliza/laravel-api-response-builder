<?php

namespace Stackmasteraliza\ApiResponse;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
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

            $this->registerTestingMacros();
        }
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
