<?php

declare(strict_types=1);

use QuixLabs\FilamentExtendable\FilamentExtendableServiceProvider;

test('Ensure provider boot without error', function (): void {
    expect($this->app->providerIsLoaded(FilamentExtendableServiceProvider::class))->toBe(true);
});
