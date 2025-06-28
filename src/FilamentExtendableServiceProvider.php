<?php

namespace QuixLabs\FilamentExtendable;

use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceFormSchemaClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceInfolistSchemaClassGenerator;
use Filament\Commands\FileGenerators\Resources\Schemas\ResourceTableClassGenerator;
use Filament\Forms\Commands\FileGenerators\FormSchemaClassGenerator;
use Filament\Schemas\Commands\FileGenerators\SchemaClassGenerator;
use Filament\Tables\Commands\FileGenerators\TableClassGenerator;
use Illuminate\Support\ServiceProvider;
use QuixLabs\FilamentExtendable\Generators\ExtendableFormSchemaClassGenerator;
use QuixLabs\FilamentExtendable\Generators\ExtendableResourceClassGenerator;
use QuixLabs\FilamentExtendable\Generators\ExtendableResourceFormSchemaClassGenerator;
use QuixLabs\FilamentExtendable\Generators\ExtendableResourceInfolistSchemaClassGenerator;
use QuixLabs\FilamentExtendable\Generators\ExtendableResourceTableClassGenerator;
use QuixLabs\FilamentExtendable\Generators\ExtendableSchemaClassGenerator;
use QuixLabs\FilamentExtendable\Generators\ExtendableTableClassGenerator;

class FilamentExtendableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerRegistry();
        $this->registerFilamentGenerators();
    }

    private function registerRegistry(): void
    {
        $this->app->scoped(FilamentExtendableManager::class);
    }

    private function registerFilamentGenerators(): void
    {
        $this->app->bind(FormSchemaClassGenerator::class, ExtendableFormSchemaClassGenerator::class);
        $this->app->bind(ResourceClassGenerator::class, ExtendableResourceClassGenerator::class);
        $this->app->bind(ResourceFormSchemaClassGenerator::class, ExtendableResourceFormSchemaClassGenerator::class);
        $this->app->bind(ResourceInfolistSchemaClassGenerator::class, ExtendableResourceInfolistSchemaClassGenerator::class);
        $this->app->bind(ResourceTableClassGenerator::class, ExtendableResourceTableClassGenerator::class);
        $this->app->bind(SchemaClassGenerator::class, ExtendableSchemaClassGenerator::class);
        $this->app->bind(TableClassGenerator::class, ExtendableTableClassGenerator::class);
    }
}
