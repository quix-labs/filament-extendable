<?php

declare(strict_types=1);

namespace QuixLabs\FilamentExtendable\Generators;

use Filament\Forms\Commands\FileGenerators\FormSchemaClassGenerator;
use Nette\PhpGenerator\Method;
use QuixLabs\FilamentExtendable\Facades\FilamentExtendable;
use RuntimeException;

class ExtendableFormSchemaClassGenerator extends FormSchemaClassGenerator
{
    public function getImports(): array
    {
        return array_merge(parent::getImports(), [FilamentExtendable::class]);
    }

    protected function configureConfigureMethod(Method $method): void
    {
        parent::configureConfigureMethod($method);
        $initialReturn = $method->getBody();

        // Extract the expression returned by the parent method, without the 'return' keyword or the trailing semicolon
        if (preg_match('/return\s+(.*?);?\s*$/s', trim($initialReturn), $matches)) {
            $schemaContent = $matches[1];
        } else {
            throw new RuntimeException("Failed to extract return expression from parent method body.");
        }

        $method->setBody("return FilamentExtendable::processSchema({$schemaContent}, static::class);");
    }
}
