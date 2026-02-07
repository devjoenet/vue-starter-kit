<?php

declare(strict_types=1);

it('matches the material input variants structure', function () {
    $path = dirname(__DIR__, 2).'/resources/js/components/ui/input/Input.vue';

    expect(file_exists($path))->toBeTrue();

    $contents = file_get_contents($path);

    expect($contents)
        ->toContain('leading-icon')
        ->toContain('trailing-icon')
        ->toContain('supportingText')
        ->toContain('maxLength')
        ->toContain('variant')
        ->toContain('outlined')
        ->toContain('filled')
        ->toContain('textarea');
});
