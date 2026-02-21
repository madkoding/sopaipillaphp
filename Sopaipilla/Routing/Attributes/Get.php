<?php

declare(strict_types=1);

namespace Sopaipilla\Routing\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Get
{
    public function __construct(public string $path) {}
}
