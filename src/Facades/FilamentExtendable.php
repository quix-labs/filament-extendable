<?php

declare(strict_types=1);

namespace QuixLabs\FilamentExtendable\Facades;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Facade;
use QuixLabs\FilamentExtendable\FilamentExtendableManager;

/**
 * @method static FilamentExtendableManager addSchemaModifier(string $identifier, callable $callback, int $priority = 0) Callables of type: function(SchemaBuilder): void
 * @method static FilamentExtendableManager addTableModifier(string $identifier, callable $callback, int $priority = 0) Callables of type: function(TableBuilder): void
 * @method static callable[] getSchemaModifiers(string $identifier, bool $sorted = true) Callables of type: function(SchemaBuilder): void
 * @method static callable[] getTableModifiers(string $identifier, bool $sorted = true) Callables of type: function(TableBuilder): void
 * @method static Schema processSchema(Schema $schema, string $identifier)
 * @method static Table processTable(Table $table, string $identifier)
 * @method static void flush()
 *
 * @method static FilamentExtendableManager getFacadeRoot()
 *
 * @see \QuixLabs\FilamentExtendable\FilamentExtendableManager
 */
class FilamentExtendable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FilamentExtendableManager::class;
    }
}
