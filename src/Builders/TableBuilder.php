<?php

declare(strict_types=1);

namespace QuixLabs\FilamentExtendable\Builders;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use Filament\Tables\Table;
use QuixLabs\FilamentExtendable\Enums\InsertPosition;
use QuixLabs\FilamentExtendable\Exceptions\TableGroupNotFoundException;
use QuixLabs\FilamentExtendable\Facades\FilamentExtendable;

class TableBuilder
{
    /**
     * @deprecated Consider using {@see FilamentExtendable::processTable()} instead
     * @see FilamentExtendable::processTable()
     */
    public static function process(Table $table, string $identifier): Table
    {
        return FilamentExtendable::processTable($table, $identifier);
    }

    /**
     * @param callable(static):void $callback
     *
     * @deprecated Consider using {@see FilamentExtendable::addTableModifier()} instead.
     * @see FilamentExtendable::addTableModifier()
     */
    public static function modifyTableUsing(string $identifier, callable $callback, int $priority = 0): void
    {
        FilamentExtendable::addTableModifier($identifier, $callback, $priority);
    }

    /*  Per-table processing */
    public function __construct(protected Table $table)
    {
    }

    /**
     * Push columns into the table or a specific group identified by label.
     *
     * If $targetGroup is provided, inserts the components into that group.
     * If $targetKey is provided, inserts components before or after that component within the group/root.
     * If $targetKey is null, inserts at the start ('before') or end ('after') of the group/root.
     *
     * @param array<Column | ColumnLayoutComponent | ColumnGroup> $columns Columns to insert
     * @param InsertPosition $position Position relative to $targetKey or at start/end if $targetKey is null
     * @param string|null $targetKey Key of the column relative to which insertion is done
     * @param string|null $targetGroup Target group label
     * @throws TableGroupNotFoundException
     */
    public function pushColumns(
        array          $columns,
        InsertPosition $position = InsertPosition::AFTER,
        ?string        $targetKey = null,
        ?string        $targetGroup = null,
    ): self
    {
        $layout = $this->table->getColumnsLayout();

        // If no target group specified, insert at root level and update schema
        if ($targetGroup === null) {
            $updated = $this->insertAt($layout, $columns, $position, $targetKey);
            $this->table->columns($updated);
            return $this;
        }

        // Retrieve the target group component
        $group = $this->table->getColumnGroup($targetGroup);
        if (!$group instanceof ColumnGroup) {
            throw new TableGroupNotFoundException($targetGroup);
        }

        // Insert columns into the target group's children directly
        $groupColumns = $group->getColumns();
        $updatedGroupColumns = $this->insertAt($groupColumns, $columns, $position, $targetKey);
        $group->columns($updatedGroupColumns);

        $this->table->columns($layout);

        return $this;
    }

    /**
     * Insert components at the given position relative to the target key.
     *
     * @param array<Column | ColumnLayoutComponent | ColumnGroup> $columns Columns where to insert
     * @param array<Column | ColumnLayoutComponent | ColumnGroup> $toInsert Columns to insert
     * @param InsertPosition $position Position relative to $targetKey
     * @param string|null $targetKey Key to find the position
     * @return array<Column | ColumnLayoutComponent | ColumnGroup> Updated columns list
     */
    protected function insertAt(
        array          $columns,
        array          $toInsert,
        InsertPosition $position,
        ?string        $targetKey,
    ): array
    {
        if ($targetKey === null) {
            return match ($position) {
                InsertPosition::BEFORE => array_merge($toInsert, $columns),
                InsertPosition::AFTER => array_merge($columns, $toInsert),
            };
        }

        foreach (array_values($columns) as $index => $column) {
            // Case: nested layout (e.g., Stack, Grid, etc.)
            if ($column instanceof ColumnLayoutComponent) {
                $updated = $this->insertAt($column->getComponents(), $toInsert, $position, $targetKey);
                $column->components($updated);
                return $columns;
            }

            // Case: flat column
            if ($column instanceof Column && $column->getName() === $targetKey) {
                return match ($position) {
                    InsertPosition::BEFORE => array_merge(
                        array_slice($columns, 0, $index, true),
                        $toInsert,
                        array_slice($columns, $index, null, true),
                    ),
                    InsertPosition::AFTER => array_merge(
                        array_slice($columns, 0, $index + 1, true),
                        $toInsert,
                        array_slice($columns, $index + 1, null, true),
                    ),
                };
            }
        }

        // If target key is not found, fallback to append/prepend
        return match ($position) {
            InsertPosition::BEFORE => array_merge($toInsert, $columns),
            InsertPosition::AFTER => array_merge($columns, $toInsert),
        };
    }

    /**
     * Remove columns from the table.
     *
     * @param string[] $keys Keys of the columns to remove
     */
    public function removeColumns(array $keys): self
    {
        $columns = $this->table->getColumns();
        foreach ($keys as $key) {
            if (isset($columns[$key])) {
                unset($columns[$key]);
            }
        }
        $this->table->columns($columns);

        return $this;
    }
}
