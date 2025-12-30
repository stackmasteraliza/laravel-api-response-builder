<?php

namespace Stackmasteraliza\ApiResponse\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ApiEndpoint
{
    public function __construct(
        public string $summary,
        public string $description = '',
        public array $tags = [],
        public bool $deprecated = false,
    ) {}
}
