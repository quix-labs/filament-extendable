<?php

declare(strict_types=1);

namespace QuixLabs\FilamentExtendable\Builders;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use QuixLabs\FilamentExtendable\Enums\InsertPosition;
use QuixLabs\FilamentExtendable\Exceptions\SchemaGroupNotFoundException;
use QuixLabs\FilamentExtendable\Facades\FilamentExtendable;

class SchemaBuilder
{
    /**
     * @deprecated Consider using {@see FilamentExtendable::processSchema()} instead
     * @see FilamentExtendable::processSchema()
     */
    public static function process(Schema $schema, string $identifier): Schema
    {
        return FilamentExtendable::processSchema($schema, $identifier);
    }

    /**
     * @param callable(static):void $callback
     *
     * @deprecated Consider using {@see FilamentExtendable::addSchemaModifier()} instead.
     * @see FilamentExtendable::addSchemaModifier()
     */
    public static function modifySchemaUsing(string $identifier, callable $callback, int $priority = 0): void
    {
        FilamentExtendable::addSchemaModifier($identifier, $callback, $priority);
    }

    /*  Per-schema processing */
    public function __construct(protected Schema $schema)
    {
    }

    /**
     * Push components into the schema or a nested group identified by dot notation.
     *
     * If $targetGroup is provided, inserts the components into that nested group.
     * If $targetKey is provided, inserts components before or after that component within the group.
     * If $targetKey is null, inserts at the start ('before') or end ('after') of the group.
     *
     * @param array<Component|Action|ActionGroup> $components Components to insert
     * @param InsertPosition $position Position relative to $targetKey or at start/end if $targetKey is null
     * @param string|null $targetKey Key of the component relative to which insertion is done
     * @param string|null $targetGroup Dot notation path to the nested group (e.g. "tabs.seo.metas")
     * @throws SchemaGroupNotFoundException
     */
    public function pushComponents(
        array          $components,
        InsertPosition $position = InsertPosition::AFTER,
        ?string        $targetKey = null,
        ?string        $targetGroup = null
    ): self
    {
        // Get root components
        $rootComponents = $this->schema->getComponents(withHidden: true);

        // If no target group specified, insert at root level and update schema
        if ($targetGroup === null) {
            $updated = $this->insertAt($rootComponents, $components, $position, $targetKey);
            $this->schema->components($updated);
            return $this;
        }

        // Retrieve the nested target group component
        $targetComponent = $this->findNestedComponent(explode('.', $targetGroup), $this->schema);
        if (!$targetComponent instanceof Component) {
            throw new SchemaGroupNotFoundException($targetGroup);
        }

        // Insert components into the target group's children directly
        $childComponents = $targetComponent->getDefaultChildComponents();
        $updatedChildren = $this->insertAt($childComponents, $components, $position, $targetKey);

        // Set updated children back into the target component (modifies by reference)
        $targetComponent->components($updatedChildren);

        return $this;
    }

    /**
     * Remove components from the schema or a nested group using their keys.
     *
     * Dot notation is supported for nested group paths (e.g. "tabs.seo.meta_title").
     *
     * @param string[] $keys Keys of the components to remove
     */
    public function removeComponents(array $keys): self
    {
        foreach ($keys as $fullKey) {
            $tree = explode('.', $fullKey);
            $fieldKey = array_pop($tree);
            $groupPath = count($tree) === 0 ? null : implode('.', $tree);

            // Return root or section/grid/tab/... when needed
            $parent = empty($groupPath) ? $this->schema : $this->schema->getComponent(implode('.', $tree));
            if (!$parent) {
                continue;
            }

            $components = match (true) {
                is_a($parent, Schema::class) => $parent->getComponents(),
                is_a($parent, Component::class) => $parent->getChildComponents(),
            };

            foreach ($components as $i => $component) {
                if ($component->getKey(false) === $fieldKey) {
                    array_splice($components, $i, 1);
                    $parent->components($components);
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Use this function because ->getComponent('path') cause OOM
     */
    protected function findNestedComponent(array $segments, Component|Schema $parent): ?Component
    {
        $currentKey = array_shift($segments);

        $children = $parent instanceof Schema
            ? $parent->getComponents(withHidden: true)
            : $parent->getDefaultChildComponents();

        foreach ($children as $child) {
            if ($child->getKey(false) !== $currentKey) {
                continue;
            }

            return $segments === []
                ? $child
                : $this->findNestedComponent($segments, $child);
        }

        return null;
    }

    /**
     * Insert components at the given position relative to the target key.
     *
     * @param array<Component|Action|ActionGroup> $components Components where to insert
     * @param array<Component|Action|ActionGroup> $toInsert Components to insert
     * @param InsertPosition $position Position relative to $targetKey
     * @param string|null $targetKey Key to find the position
     * @return array<Component|Action|ActionGroup> Updated components list
     */
    protected function insertAt(array $components, array $toInsert, InsertPosition $position, ?string $targetKey): array
    {
        if ($targetKey === null) {
            return match ($position) {
                InsertPosition::BEFORE => [...$toInsert, ...$components],
                InsertPosition::AFTER => [...$components, ...$toInsert],
            };
        }

        foreach (array_values($components) as $index => $component) {
            if ($component->getKey(false) === $targetKey) {
                return match ($position) {
                    InsertPosition::BEFORE => array_merge(
                        array_slice($components, 0, $index, true),
                        $toInsert,
                        array_slice($components, $index, null, true),
                    ),
                    InsertPosition::AFTER => array_merge(
                        array_slice($components, 0, $index + 1, true),
                        $toInsert,
                        array_slice($components, $index + 1, null, true),
                    ),
                };
            }
        }

        // targetKey not found, append/prepend to original array
        return match ($position) {
            InsertPosition::BEFORE => [...$toInsert, ...$components],
            InsertPosition::AFTER => [...$components, ...$toInsert],
        };
    }
}
