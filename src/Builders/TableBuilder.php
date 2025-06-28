<?php

namespace QuixLabs\FilamentExtendable\Builders;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use Filament\Tables\Table;

class TableBuilder
{
    private static array $modifiers = [];

    public static function process(Table $table, string $identifier): Table
    {
        $builder = new static($table);

        foreach (static::$modifiers[$identifier] ?? [] as $priority => $modifiers) {
            foreach ($modifiers as $modifier) {
                $modifier($builder);
            }
        }

        return $table;
    }

    public static function modifyTableUsing(string $identifier, callable $callback, int $priority = 0): void
    {
        static::$modifiers[$identifier] ??= [];
        static::$modifiers[$identifier][$priority] ??= [];
        static::$modifiers[$identifier][$priority][] = $callback;
    }

    /*  Per-table processing */
    public function __construct(protected Table $table)
    {
    }

    /**
     * Push columns to the end of the column list.
     *
     * @param array<Column | ColumnLayoutComponent | ColumnGroup> $components
     */
    public function pushColumns(array $components): static
    {
        $this->table->pushColumns($components);
        return $this;
    }

    /**
     * Insert components after a specific target column by name.
     *
     * @param string $targetName The name of the column to insert after.
     * @param array<Column | ColumnLayoutComponent | ColumnGroup> $components
     */
    public function insertAfter(string $targetName, array $components): static
    {
        $this->insertRelativeTo($targetName, $components, after: true);
        return $this;
    }

    /**
     * Insert components before a specific target column by name.
     *
     * @param string $targetName The name of the column to insert before.
     * @param array<Column | ColumnLayoutComponent | ColumnGroup> $components
     */
    public function insertBefore(string $targetName, array $components): static
    {
        $this->insertRelativeTo($targetName, $components, after: false);
        return $this;
    }


    /**
     * Internal logic to insert before or after a column by name in the top-level columns array.
     */
    protected function insertRelativeTo(string $targetName, array $components, bool $after = true): void
    {
        $columns = $this->table->getColumns();
        $this->table->columns(
            $this->insertIntoArrayByName($columns, $targetName, $components, $after)
        );
    }

    /**
     * Helper method to insert components into an array before or after a target item by its name.
     *
     * @param array<Column | ColumnLayoutComponent | ColumnGroup> $original
     * @param string $targetName
     * @param array<Column | ColumnLayoutComponent | ColumnGroup> $components
     * @param bool $after
     * @return array
     */
    protected function insertIntoArrayByName(array $original, string $targetName, array $components, bool $after): array
    {
        $result = [];
        $matched = false;

        foreach ($original as $column) {
            if ($column->getName() === $targetName) {
                $matched = true;
                if (!$after) {
                    $result = array_merge($result, $components);
                }

                $result[] = $column;

                if ($after) {
                    $result = array_merge($result, $components);
                }
            } else {
                $result[] = $column;
            }
        }

        return $matched ? $result : array_merge($original, $components);
    }
}
