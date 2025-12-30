<?php

namespace Stackmasteraliza\ApiResponse\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ApiSchema
{
    public function __construct(
        public string $name,
        public string $description = '',
        public array $properties = [],
    ) {}
}
