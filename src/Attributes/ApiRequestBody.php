<?php

namespace Stackmasteraliza\ApiResponse\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ApiRequestBody
{
    public function __construct(
        public array $properties = [],
        public array $required = [],
        public string $description = '',
        public ?array $example = null,
    ) {}
}
