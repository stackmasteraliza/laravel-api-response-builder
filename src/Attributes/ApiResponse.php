<?php

namespace Stackmasteraliza\ApiResponse\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ApiResponse
{
    public function __construct(
        public int $status = 200,
        public string $description = 'Successful response',
        public ?array $example = null,
        public ?string $ref = null,
    ) {}
}
