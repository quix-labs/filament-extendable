<?php

declare(strict_types=1);

namespace QuixLabs\FilamentExtendable\Exceptions;

use Exception;
use Throwable;

class SchemaGroupNotFoundException extends Exception
{
    public function __construct(string $targetGroup = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = "Target group '{$targetGroup}' not found. Try adjusting your priority or verifying the group path.";
        parent::__construct($message, $code, $previous);
    }
}
