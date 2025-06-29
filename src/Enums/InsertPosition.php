<?php

declare(strict_types=1);

namespace QuixLabs\FilamentExtendable\Enums;

enum InsertPosition: string
{
    case BEFORE = 'before';
    case AFTER = 'after';
}
