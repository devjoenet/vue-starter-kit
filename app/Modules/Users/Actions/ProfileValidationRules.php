<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Users\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Validation\Rule;
use Stringable;

final class ProfileValidationRules
{
    /** @return array<int, Stringable|ValidationRule|array|string> */
    public static function email(?int $userId = null): array
    {
        $rule = Rule::unique(User::class)
            ->where(fn (QueryBuilder $query): QueryBuilder => $query->whereNull('deleted_at'));

        if ($userId !== null) {
            $rule = $rule->ignore($userId);
        }

        return [
            'required',
            'string',
            'email',
            'max:255',
            $rule,
        ];
    }

    /** @return array<int, Stringable|ValidationRule|array|string> */
    public static function name(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /** @return array<string, array<int, Stringable|ValidationRule|array|string>> */
    public static function profile(?int $userId = null): array
    {
        return [
            'name' => self::name(),
            'email' => self::email($userId),
        ];
    }
}
