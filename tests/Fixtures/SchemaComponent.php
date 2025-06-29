<?php

declare(strict_types=1);

namespace QuixLabs\FilamentExtendable\Tests\Fixtures;

use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;

class SchemaComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public static function make(): static
    {
        return new static;
    }
}
