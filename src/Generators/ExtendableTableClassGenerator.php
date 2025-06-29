<?php

declare(strict_types=1);

namespace QuixLabs\FilamentExtendable\Generators;

use RuntimeException;
use Filament\Tables\Commands\FileGenerators\TableClassGenerator;
use Nette\PhpGenerator\Method;
use QuixLabs\FilamentExtendable\Builders\TableBuilder;

class ExtendableTableClassGenerator extends TableClassGenerator
{
    public function getImports(): array
    {
        return array_merge(parent::getImports(), [TableBuilder::class]);
    }

    protected function configureConfigureMethod(Method $method): void
    {
        parent::configureConfigureMethod($method);
        $initialReturn = $method->getBody();

        // Extract the expression returned by the parent method, without the 'return' keyword or the trailing semicolon
        if (preg_match('/return\s+(.*?);?\s*$/s', trim($initialReturn), $matches)) {
            $tableContent = $matches[1];
        } else {
            throw new RuntimeException("Failed to extract return expression from parent method body.");
        }

        $method->setBody("return TableBuilder::process({$tableContent}, static::class);");
    }
}
