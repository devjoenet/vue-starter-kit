<?php

declare(strict_types=1);

it('exposes string case conversion helpers in utils', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/lib/utils.ts');

    expect($contents)
        ->toContain('function toWords(value: string): string[]')
        ->toContain('export function toSnakeCase(value: string): string')
        ->toContain('export function toKebabCase(value: string): string')
        ->toContain('export function toTitleCase(value: string): string')
        ->toContain('export function toStudlyCase(value: string): string')
        ->toContain('export function toCamelCase(value: string): string')
        ->toContain('.replace(/([a-z0-9])([A-Z])/g, "$1 $2")')
        ->toContain('.replace(/[_\-.]+/g, " ")');
});
