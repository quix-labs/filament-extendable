<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use QuixLabs\FilamentExtendable\Builders\SchemaBuilder;
use QuixLabs\FilamentExtendable\Builders\TableBuilder;
use QuixLabs\FilamentExtendable\Tests\Fixtures\SchemaComponent;
use QuixLabs\FilamentExtendable\Tests\Fixtures\TableComponent;

test('Ensure schema can be extended at runtime', function () {
    static $identifier = "test-schema";
    SchemaBuilder::modifySchemaUsing($identifier, function (SchemaBuilder $schemaBuilder) {
        $schemaBuilder->pushComponents([TextInput::make('password')]);
    });

    $schema = SchemaBuilder::process(
        schema: Schema::make(SchemaComponent::make())->components([TextInput::make('name')]),
        identifier: $identifier
    );

    $keys = array_keys($schema->getFlatComponents());
    expect($keys)->toBe(['name', 'password']);
});

test('Ensure schema modifiers are executed in order', function () {
    static $identifier = "test-schema-order";

    SchemaBuilder::modifySchemaUsing($identifier, function (SchemaBuilder $schemaBuilder) {
        $schemaBuilder->pushComponents([TextInput::make('password_20')]);
    }, 20);

    SchemaBuilder::modifySchemaUsing($identifier, function (SchemaBuilder $schemaBuilder) {
        $schemaBuilder->pushComponents([TextInput::make('password_10')]);
    }, 10);

    $schema = SchemaBuilder::process(
        schema: Schema::make(SchemaComponent::make())->components([TextInput::make('name')]),
        identifier: $identifier
    );

    $keys = array_keys($schema->getFlatComponents());
    expect($keys)->toBe(['name', 'password_10', 'password_20']);
});

test('Ensure table can be extended at runtime', function () {
    static $identifier = "test-table";
    TableBuilder::modifyTableUsing($identifier, function (TableBuilder $tableBuilder) {
        $tableBuilder->pushColumns([TextColumn::make('password')]);
    });

    $table = TableBuilder::process(
        table: Table::make(TableComponent::make())->columns([TextColumn::make('name')]),
        identifier: $identifier
    );

    $keys = array_keys($table->getColumns());
    expect($keys)->toBe(['name', 'password']);
});

test('Ensure table modifiers are executed in order', function () {
    static $identifier = "test-table-order";
    TableBuilder::modifyTableUsing($identifier, function (TableBuilder $tableBuilder) {
        $tableBuilder->pushColumns([TextColumn::make('password_20')]);
    }, 20);
    TableBuilder::modifyTableUsing($identifier, function (TableBuilder $tableBuilder) {
        $tableBuilder->pushColumns([TextColumn::make('password_10')]);
    }, 10);

    $table = TableBuilder::process(
        table: Table::make(TableComponent::make())->columns([TextColumn::make('name')]),
        identifier: $identifier
    );

    $keys = array_keys($table->getColumns());
    expect($keys)->toBe(['name', 'password_10', 'password_20']);
});
