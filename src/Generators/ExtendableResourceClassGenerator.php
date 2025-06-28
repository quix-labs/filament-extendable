<?php

namespace QuixLabs\FilamentExtendable\Generators;

use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator;
use Nette\PhpGenerator\Method;
use QuixLabs\FilamentExtendable\Builders\SchemaBuilder;
use QuixLabs\FilamentExtendable\Builders\TableBuilder;

class ExtendableResourceClassGenerator extends ResourceClassGenerator
{
    public function getImports(): array
    {
        if (!$this->isSimple()) {
            return parent::getImports();
        }
        return array_merge(parent::getImports(), [SchemaBuilder::class, TableBuilder::class]);
    }

    protected function configureInfolistMethod(Method $method): void
    {
        parent::configureInfolistMethod($method);
        if ($this->isSimple()) {
            $this->injectSchemaBuilderInMethodBody($method, "static::class . ':infolist'");
        }
    }

    protected function configureFormMethod(Method $method): void
    {
        parent::configureFormMethod($method);
        if ($this->isSimple()) {
            $this->injectSchemaBuilderInMethodBody($method, "static::class . ':form'");
        }
    }

    protected function configureTableMethod(Method $method): void
    {
        parent::configureTableMethod($method);
        if ($this->isSimple()) {
            $this->injectTableBuilderInMethodBody($method, "static::class");
        }
    }

    protected function injectSchemaBuilderInMethodBody(Method $method, string $identifier = "static::class"): void
    {
        $initialReturn = $method->getBody();

        // Extract the expression returned by the parent method, without the 'return' keyword or the trailing semicolon
        if (preg_match('/return\s+(.*?);?\s*$/s', trim($initialReturn), $matches)) {
            $schemaContent = $matches[1];
        } else {
            throw new \RuntimeException("Failed to extract return expression from parent method body.");
        }

        $method->setBody("return SchemaBuilder::process($schemaContent, $identifier);");
    }

    protected function injectTableBuilderInMethodBody(Method $method, string $identifier = "static::class"): void
    {
        $initialReturn = $method->getBody();

        // Extract the expression returned by the parent method, without the 'return' keyword or the trailing semicolon
        if (preg_match('/return\s+(.*?);?\s*$/s', trim($initialReturn), $matches)) {
            $tableContent = $matches[1];
        } else {
            throw new \RuntimeException("Failed to extract return expression from parent method body.");
        }

        $method->setBody("return TableBuilder::process($tableContent, $identifier);");
    }
}
