<?php

use QuixLabs\FilamentExtendable\FilamentExtendableServiceProvider;

test('Ensure provider boot without error', function () {
    expect($this->app->providerIsLoaded(FilamentExtendableServiceProvider::class))->toBe(true);
});
