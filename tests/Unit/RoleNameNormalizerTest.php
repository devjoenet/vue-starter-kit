<?php

declare(strict_types=1);

use App\Modules\Roles\Actions\RoleNameNormalizer;

it('normalizes role names to kebab-case', function (): void {
    $normalizer = app(RoleNameNormalizer::class);

    expect($normalizer->normalize('Support Team Lead'))->toBe('support-team-lead');
    expect($normalizer->normalize('SupportManager'))->toBe('support-manager');
    expect($normalizer->normalize('  Billing   Ops  '))->toBe('billing-ops');
});
