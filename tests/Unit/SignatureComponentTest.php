<?php

declare(strict_types=1);

it('matches the enhanced signature structure', function () {
    $path = dirname(__DIR__, 2).'/resources/js/components/ui/signature/Signature.vue';
    $indexPath = dirname(__DIR__, 2).'/resources/js/components/ui/signature/index.ts';

    expect(file_exists($path))->toBeTrue();
    expect(file_exists($indexPath))->toBeTrue();

    $contents = file_get_contents($path);
    $indexContents = file_get_contents($indexPath);

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

    expect($indexContents)
        ->toContain('SignatureDataUrlType')
        ->toContain('signatureSurfaceVariants')
        ->toContain('error')
        ->toContain('success');
});
