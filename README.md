# 🧩 Filament Extendable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/quix-labs/filament-extendable.svg?style=flat-square)](https://packagist.org/packages/quix-labs/filament-extendable)
[![Total Downloads](https://img.shields.io/packagist/dt/quix-labs/filament-extendable.svg?style=flat-square)](https://packagist.org/packages/quix-labs/filament-extendable)

**Filament Extendable** is a powerful extension toolkit for [FilamentPHP](https://filamentphp.com/), enabling highly
composable and dynamic customization of tables, forms, and infolists — all without touching core logic.

## 🚩 Important Notice

> This library is under active development.
> APIs and features may evolve.
> Contributions and feedback are highly appreciated.

## Features

* Dynamic registration of modifiers for **forms**, **tables**, and **infolists**
* Insert components **before** or **after** existing ones
* Remove components by key
* Full compatibility with `filament:make-*` commands
* Minimal interference with native Filament lifecycle

## Requirements

- PHP `^8.2`
- Laravel `^11.x || ^12.x`
- Filament `^4.0`

## 📦 Installation

```bash
composer require quix-labs/filament-extendable
```

If needed, register the service provider (usually auto-discovered):

```php
// config/app.php
'providers' => [
    // ...
    QuixLabs\FilamentExtendable\FilamentExtendableServiceProvider::class,
],
```

## Usage

### 1. Enable Extensibility in Your Resource

Wrap your resource schema and tables with the builders:

```php
use QuixLabs\FilamentExtendable\Builders\TableBuilder;
use QuixLabs\FilamentExtendable\Builders\SchemaBuilder;

class UserResource extends Resource
{
    public static function form(Schema $schema): Schema
    {
        return SchemaBuilder::process($schema, 'user-form');
    }

    public static function infolist(Schema $schema): Schema
    {
        return SchemaBuilder::process($schema, 'user-infolist');
    }

    public static function table(Table $table): Table
    {
        return TableBuilder::process($table, 'user-table');
    }
}
```

### 2. Register Modifiers

Define dynamic modifiers in the `boot` function of your service provider:

```php
use QuixLabs\FilamentExtendable\Builders\TableBuilder;
use QuixLabs\FilamentExtendable\Builders\SchemaBuilder;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

class FilamentExtendServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Insert a column after 'email' in the user table
        TableBuilder::modifyTableUsing('user-table', function (TableBuilder $tableBuilder) {
            $tableBuilder->insertAfter('email', [
                TextColumn::make('lastname')->label('Last Name'),
            ]);
        });

        // Add a field to the user infolist
        SchemaBuilder::modifySchemaUsing('user-infolist', function (SchemaBuilder $schemaBuilder) {
            $schemaBuilder->pushComponents([
                TextEntry::make('lastname')->label('Last Name'),
            ]);
        });
    }
}
```

### 3. Schema Manipulations

#### Insert component in schema

Push components at root or inside nested groups via dot notation:

Control insertion with:

* `position` — `'before'` or `'after'` relative to a target key
* `targetKey` — component key near which to insert
* `targetGroup` — dot notation path to nested group

```php
// Append at root level (default behavior)
$schemaBuilder->pushComponents([
    TextInput::make('nickname')->label('Nickname'),
]);

// After 'bio' at root level
$schemaBuilder->pushComponents([
    TextArea::make('additional_notes'),
], position: InsertPosition::AFTER, targetKey: 'bio');

// Before 'email' at root level
$schemaBuilder->pushComponents([
    Select::make('preferred_contact_method'),
], position: InsertPosition::BEFORE, targetKey: 'email');

// After 'last_login' in 'tabs.activity' nested tab/section/group/...
$schemaBuilder->pushComponents([
    TextEntry::make('last_login_ip')->label('Last Login IP'),
], position: InsertPosition::AFTER, targetKey: 'last_login', targetGroup: 'tabs.activity');
```

#### Delete Components from Schema

You can remove components from both the root level and nested groups using their keys.

**If a specified key is not found, the operation is silently skipped (no error thrown).**

```php
$schemaBuilder->removeComponents([
    'email', // Removes 'email' field from the root level
    'tabs.seo.meta_title', // Dot notation is supported for nested paths.
]);
```

## Integration with Filament Artisan Commands

Filament Extendable patches the following commands to scaffold extendable resources automatically:

```bash
php artisan make:filament-resource User
php artisan make:filament-table UserTable
php artisan make:filament-form UserForm
php artisan make:filament-schema UserSchema
```

They generate boilerplate code that includes calls to `SchemaBuilder::process()` and `TableBuilder::process()`, ready
for modifier registration.

> 💡 This allows you to plug in modifiers immediately, without writing boilerplate.

## Why this exists

Filament is an exceptional tool, but there are scenarios where additional dynamic control is needed:

* Modular package systems needing to inject UI logic
* Enterprise-grade systems with conditional rendering needs
* Reusable and declarative customizations across multiple resources

**Filament Extendable** solves this with a non-intrusive, declarative API.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🤝 Contributing

We welcome your contributions!

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Commit your changes.
4. Push your branch.
5. Create a pull request.

## Credits

- [COLANT Alan](https://github.com/alancolant)
- [Uni-Deal](https://github.com/uni-deal)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


