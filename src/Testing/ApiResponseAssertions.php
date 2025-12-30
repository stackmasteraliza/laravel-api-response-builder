<?php

namespace Stackmasteraliza\ApiResponse\Testing;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;

trait ApiResponseAssertions
{
    /**
     * Assert the response is a successful API response.
     */
    public function assertApiSuccess(): self
    {
        $this->assertJson(['success' => true]);

        return $this;
    }

    /**
     * Assert the response is an error API response.
     */
    public function assertApiError(?int $statusCode = null): self
    {
        $this->assertJson(['success' => false]);

        if ($statusCode !== null) {
            $this->assertStatus($statusCode);
        }

        return $this;
    }

    /**
     * Assert the response has a specific status code in the body.
     */
    public function assertApiStatusCode(int $statusCode): self
    {
        $data = $this->json();
        $key = config('api-response.keys.status_code', 'status_code');

        Assert::assertArrayHasKey($key, $data, "Response does not contain '{$key}' key.");
        Assert::assertEquals($statusCode, $data[$key], "Expected status code {$statusCode}, got {$data[$key]}.");

        return $this;
    }

    /**
     * Assert the response has a specific message.
     */
    public function assertApiMessage(string $message): self
    {
        $key = config('api-response.keys.message', 'message');

        $this->assertJson([$key => $message]);

        return $this;
    }

    /**
     * Assert the response has data with a specific key.
     */
    public function assertApiHasData(?string $key = null): self
    {
        $dataKey = config('api-response.keys.data', 'data');
        $data = $this->json();

        Assert::assertArrayHasKey($dataKey, $data, "Response does not contain '{$dataKey}' key.");

        if ($key !== null) {
            Assert::assertArrayHasKey($key, $data[$dataKey], "Data does not contain '{$key}' key.");
        }

        return $this;
    }

    /**
     * Assert the response data has a specific count.
     */
    public function assertApiDataCount(int $count): self
    {
        $dataKey = config('api-response.keys.data', 'data');
        $data = $this->json();

        Assert::assertArrayHasKey($dataKey, $data, "Response does not contain '{$dataKey}' key.");
        Assert::assertCount($count, $data[$dataKey], "Expected data count {$count}, got " . count($data[$dataKey]) . ".");

        return $this;
    }

    /**
     * Assert the response is paginated.
     */
    public function assertApiPaginated(): self
    {
        $metaKey = config('api-response.keys.meta', 'meta');
        $data = $this->json();

        Assert::assertArrayHasKey($metaKey, $data, "Response does not contain '{$metaKey}' key.");

        return $this;
    }

    /**
     * Assert the response is cursor paginated.
     */
    public function assertApiCursorPaginated(): self
    {
        $metaKey = config('api-response.keys.meta', 'meta');
        $data = $this->json();

        Assert::assertArrayHasKey($metaKey, $data, "Response does not contain '{$metaKey}' key.");
        Assert::assertArrayHasKey('next_cursor', $data[$metaKey], "Meta does not contain 'next_cursor' key.");

        return $this;
    }

    /**
     * Assert the response has specific errors.
     */
    public function assertApiHasErrors(?string $key = null): self
    {
        $errorsKey = config('api-response.keys.errors', 'errors');
        $data = $this->json();

        Assert::assertArrayHasKey($errorsKey, $data, "Response does not contain '{$errorsKey}' key.");

        if ($key !== null) {
            Assert::assertArrayHasKey($key, $data[$errorsKey], "Errors does not contain '{$key}' key.");
        }

        return $this;
    }

    /**
     * Assert the response data equals the expected value.
     */
    public function assertApiData(array $expected): self
    {
        $dataKey = config('api-response.keys.data', 'data');

        $this->assertJson([$dataKey => $expected]);

        return $this;
    }

    /**
     * Assert the response data contains the expected subset.
     */
    public function assertApiDataContains(array $expected): self
    {
        $dataKey = config('api-response.keys.data', 'data');
        $data = $this->json();

        Assert::assertArrayHasKey($dataKey, $data, "Response does not contain '{$dataKey}' key.");

        foreach ($expected as $key => $value) {
            Assert::assertArrayHasKey($key, $data[$dataKey], "Data does not contain '{$key}' key.");
            Assert::assertEquals($value, $data[$dataKey][$key], "Data '{$key}' does not match expected value.");
        }

        return $this;
    }
}
