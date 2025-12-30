<?php

namespace Stackmasteraliza\ApiResponse\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ApiRequest
{
    public function __construct(
        public string $name,
        public string $type = 'string',
        public string $in = 'query',
        public string $description = '',
        public bool $required = false,
        public mixed $example = null,
    ) {}
}
