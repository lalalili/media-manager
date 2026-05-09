<?php

it('requires an explicit tenant context', function (): void {
    $this->artisan('media-manager:ensure-root-folders')
        ->expectsOutput('Configured folder model does not exist.')
        ->assertFailed();
});
