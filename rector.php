<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use RectorLaravel\Rector\Class_\AnonymousMigrationsRector;
use RectorLaravel\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector;
use RectorLaravel\Rector\Expr\AppEnvironmentComparisonToParameterRector;
use RectorLaravel\Rector\FuncCall\FactoryFuncCallToStaticCallRector;
use RectorLaravel\Rector\FuncCall\NowFuncWithStartOfDayMethodCallToTodayFuncRector;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Rector\FuncCall\SleepFuncToSleepStaticCallRector;
use RectorLaravel\Rector\If_\ReportIfRector;
use RectorLaravel\Rector\MethodCall\AssertStatusToAssertMethodRector;
use RectorLaravel\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector;
use RectorLaravel\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector;
use RectorLaravel\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector;
use RectorLaravel\Rector\MethodCall\JsonCallToExplicitJsonCallRector;
use RectorLaravel\Rector\MethodCall\RedirectBackToBackHelperRector;
use RectorLaravel\Rector\MethodCall\ResponseHelperCallToJsonResponseRector;
use RectorLaravel\Rector\MethodCall\UseComponentPropertyWithinCommandsRector;
use RectorLaravel\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector;
use RectorLaravel\Rector\Namespace_\FactoryDefinitionRector;
use RectorLaravel\Rector\PropertyFetch\OptionalToNullsafeOperatorRector;
use RectorLaravel\Rector\StaticCall\CarbonSetTestNowToTravelToRector;
use RectorLaravel\Rector\StaticCall\RequestStaticValidateToInjectRector;
use RectorLaravel\Rector\StaticCall\RouteActionCallableRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
        carbon: true,
        phpunitCodeQuality: true,
    )
    ->withPhpSets()
    ->withSets([
        LevelSetList::UP_TO_PHP_84,

        LaravelSetList::LARAVEL_100,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,

        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
        LaravelSetList::ARRAY_STR_FUNCTIONS_TO_STATIC_CALL,
    ])
    ->withRules([
        WrapEncapsedVariableInCurlyBracesRector::class,

        AnonymousMigrationsRector::class,
        AppEnvironmentComparisonToParameterRector::class,
        ApplyDefaultInsteadOfNullCoalesceRector::class,
        AssertStatusToAssertMethodRector::class,
        CarbonSetTestNowToTravelToRector::class,
        EloquentOrderByToLatestOrOldestRector::class,
        EloquentWhereRelationTypeHintingParameterRector::class,
        EloquentWhereTypeHintClosureParameterRector::class,
        FactoryDefinitionRector::class,
        FactoryFuncCallToStaticCallRector::class,
        JsonCallToExplicitJsonCallRector::class,
        NowFuncWithStartOfDayMethodCallToTodayFuncRector::class,
        OptionalToNullsafeOperatorRector::class,
        RedirectBackToBackHelperRector::class,
        RemoveDumpDataDeadCodeRector::class,
        ReportIfRector::class,
        RequestStaticValidateToInjectRector::class,
        ResponseHelperCallToJsonResponseRector::class,
        RouteActionCallableRector::class,
        SleepFuncToSleepStaticCallRector::class,

        UseComponentPropertyWithinCommandsRector::class,
        ValidationRuleArrayStringValueToArrayRector::class,
    ])
    ->withSkip([
        DisallowedEmptyRuleFixerRector::class,  // We will continue to use empty(), as it is more readable than alternatives
    ])
    ->withImportNames(
        removeUnusedImports: true
    )
    ->withCache(
        __DIR__ . '/.rector.cache'
    );
