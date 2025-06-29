<?php

declare(strict_types=1);

namespace QuixLabs\FilamentExtendable;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use QuixLabs\FilamentExtendable\Builders\SchemaBuilder;
use QuixLabs\FilamentExtendable\Builders\TableBuilder;

class FilamentExtendableManager
{
    /**
     * @var array<string,array<int,list<callable(SchemaBuilder):void>>>
     */
    private array $schemaModifiers = [];

    /**
     * @var array<string,array<int,list<callable(TableBuilder):void>>>
     */
    private array $tableModifiers = [];

    /**
     * @param callable(SchemaBuilder):void $callback
     */
    public function addSchemaModifier(string $identifier, callable $callback, int $priority = 0): self
    {
        $this->schemaModifiers[$identifier] ??= [];
        $this->schemaModifiers[$identifier][$priority] ??= [];
        $this->schemaModifiers[$identifier][$priority][] = $callback;

        return $this;
    }

    /**
     * @param callable(TableBuilder):void $callback
     */
    public function addTableModifier(string $identifier, callable $callback, int $priority = 0): self
    {
        $this->tableModifiers[$identifier] ??= [];
        $this->tableModifiers[$identifier][$priority] ??= [];
        $this->tableModifiers[$identifier][$priority][] = $callback;
        return $this;
    }

    /**
     * @return array<callable(SchemaBuilder): void>
     */
    public function getSchemaModifiers(string $identifier, bool $sorted = true): array
    {
        $modifiers = $this->schemaModifiers[$identifier] ?? [];
        if ($sorted) {
            ksort($modifiers);
        }
        // Flatten and return all callables in order
        return array_merge(...array_values($modifiers));
    }

    /**
     * @return array<callable(TableBuilder): void>
     */
    public function getTableModifiers(string $identifier, bool $sorted = true): array
    {
        $modifiers = $this->tableModifiers[$identifier] ?? [];
        if ($sorted) {
            ksort($modifiers);
        }
        // Flatten and return all callables in order
        return array_merge(...array_values($modifiers));
    }

    public function processSchema(Schema $schema, string $identifier): Schema
    {
        $builder = new SchemaBuilder($schema);
        foreach ($this->getSchemaModifiers($identifier) as $modifier) {
            $modifier($builder);
        }
        return $schema;
    }

    public function processTable(Table $table, string $identifier): Table
    {
        $builder = new TableBuilder($table);
        foreach ($this->getTableModifiers($identifier) as $modifier) {
            $modifier($builder);
        }
        return $table;
    }

    /**
     * Clear all registered modifiers
     */
    public function flush(): void
    {
        $this->schemaModifiers = [];
        $this->tableModifiers = [];
    }
}
