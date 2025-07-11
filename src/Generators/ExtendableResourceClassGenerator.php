<?php

declare(strict_types=1);

namespace QuixLabs\FilamentExtendable\Generators;

use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator;
use Nette\PhpGenerator\Method;
use QuixLabs\FilamentExtendable\Facades\FilamentExtendable;
use RuntimeException;

class ExtendableResourceClassGenerator extends ResourceClassGenerator
{
    public function getImports(): array
    {
        if (!$this->isSimple()) {
            return parent::getImports();
        }
        return array_merge(parent::getImports(), [FilamentExtendable::class]);
    }

    protected function configureInfolistMethod(Method $method): void
    {
        parent::configureInfolistMethod($method);
        if (!filled($this->getInfolistSchemaFqn())) {
            $this->injectSchemaBuilderInMethodBody($method, "{$this->getBasename()}::class . ':infolist'");
        }
    }

    protected function configureFormMethod(Method $method): void
    {
        parent::configureFormMethod($method);
        if (!filled($this->getFormSchemaFqn())) {
            $this->injectSchemaBuilderInMethodBody($method, "{$this->getBasename()}::class . ':form'");
        }
    }

    protected function configureTableMethod(Method $method): void
    {
        parent::configureTableMethod($method);
        if (!filled($this->getTableFqn())) {
            $this->injectTableBuilderInMethodBody($method, "{$this->getBasename()}::class");
        }
    }

    protected function injectSchemaBuilderInMethodBody(Method $method, string $identifier = "static::class"): void
    {
        $initialReturn = $method->getBody();

        // Extract the expression returned by the parent method, without the 'return' keyword or the trailing semicolon
        if (preg_match('/return\s+(.*?);?\s*$/s', trim($initialReturn), $matches)) {
            $schemaContent = $matches[1];
        } else {
            throw new RuntimeException("Failed to extract return expression from parent method body.");
        }

        $method->setBody("return FilamentExtendable::processSchema({$schemaContent}, {$identifier});");
    }

    protected function injectTableBuilderInMethodBody(Method $method, string $identifier = "static::class"): void
    {
        $initialReturn = $method->getBody();

        // Extract the expression returned by the parent method, without the 'return' keyword or the trailing semicolon
        if (preg_match('/return\s+(.*?);?\s*$/s', trim($initialReturn), $matches)) {
            $tableContent = $matches[1];
        } else {
            throw new RuntimeException("Failed to extract return expression from parent method body.");
        }

        $method->setBody("return FilamentExtendable::processTable({$tableContent}, {$identifier});");
    }
}
