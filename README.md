# 🧩 Filament Extendable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/quix-labs/filament-extendable.svg?style=flat-square)](https://packagist.org/packages/quix-labs/filament-extendable)
[![Total Downloads](https://img.shields.io/packagist/dt/quix-labs/filament-extendable.svg?style=flat-square)](https://packagist.org/packages/quix-labs/filament-extendable)

**Filament Extendable** is a powerful extension toolkit for [FilamentPHP](https://filamentphp.com/), designed to make
your tables, infolists and forms highly composable, customizable, and dynamically extendable — without modifying core
logic.

## Notes

> **This library is currently under active development.**
>
> Features and APIs may change.
>
> Contributions and feedback are welcome!

## Features

- Register dynamic modifiers for **forms**, **tables**, and **infolists**
- Inject components **before** or **after** existing ones
- Remove specific components by name
- Compatible with `filament:make-*` commands
- Minimal impact on the native Filament lifecycle

## Requirements

- PHP `^8.2`
- Laravel `^11.x || ^12.x`
- Filament `^4.0`

## 📦 Installation

```bash
composer require quix-labs/filament-extendable
```

## ⚙️ Usage

### 1. Enable extensibility in your Resource

```php
use QuixLabs\LaravelHookSystem\Builders\TableBuilder;
use QuixLabs\LaravelHookSystem\Builders\SchemaBuilder;

class YourResource extends Resource
{
    // ...
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
    // ...
}
```

### 2. Register a modifier

```php
use QuixLabs\FilamentExtendable\Builders\TableBuilder;
use QuixLabs\FilamentExtendable\Builders\SchemaBuilder;

class YourProvider extends ServiceProvider {
    // ...
    public function boot(): void
    {
        // Extend the table
        TableBuilder::modifyTableUsing('user-table', function (TableBuilder $builder) {
            $builder->insertAfter('email', [
                TextColumn::make('Lastname')
            ]);
        });
        
        // Extend the infolist
        SchemaBuilder::modifySchemaUsing('user-infolist', function (SchemaBuilder $builder) {
            $builder->pushComponents([
                TextEntry::make('Lastname')
            ]);
        });
        
        // Extend the form
        SchemaBuilder::modifySchemaUsing('user-form', function (SchemaBuilder $builder) {
            $builder->pushComponents([
                TextInpu::make('Lastname')
            ]);
        });
    }
}
```

### 3. Using `Native Filament Make Commands`

Filament Extendable comes with patched artisan commands to automatically generate extendable resource structures:

```bash
php artisan make:filament-resource User
php artisan make:filament-table    UserTable
php artisan make:filament-form     UserForm
php artisan make:filament-schema   UserSchema
```

These commands will:

* Scaffold the resource or schema file
* Add the appropriate call to `SchemaBuilder::process()` or `TableBuilder::process()`
* Pre-configure the identifier used for hook registration

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


