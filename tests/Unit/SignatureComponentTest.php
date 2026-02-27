<?php

declare(strict_types=1);

it('matches the enhanced signature structure', function () {
    $path = dirname(__DIR__, 2).'/resources/js/components/ui/signature/Signature.vue';
    $stylesPath = dirname(__DIR__, 2).'/resources/js/components/ui/signature/styles.ts';

    expect(file_exists($path))->toBeTrue();
    expect(file_exists($stylesPath))->toBeTrue();

    $contents = file_get_contents($path);
    $stylesContents = file_get_contents($stylesPath);

    expect($contents)
        ->toContain('lineWidth?: number')
        ->toContain('strokeStyle?: string')
        ->toContain('dataUrlType?: SignatureDataUrlType')
        ->toContain('handlePointerDown')
        ->toContain('handlePointerMove')
        ->toContain('finishDrawing')
        ->toContain('confirmSignature')
        ->toContain('defineExpose')
        ->toContain('@pointerdown="handlePointerDown"');

    expect($stylesContents)
        ->toContain('SignatureDataUrlType')
        ->toContain('signatureSurfaceVariants')
        ->toContain('error')
        ->toContain('success');
});
