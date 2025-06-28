<?php

namespace QuixLabs\FilamentExtendable\Builders;

use Filament\Schemas\Schema;

class SchemaBuilder
{
    public static function process(Schema $schema, string $identifier): Schema
    {
        return $schema;
    }
}
