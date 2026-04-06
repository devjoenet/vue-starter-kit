<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\warning;

abstract class BaseInteractiveCreateCommand extends Command
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $rules
     * @param  array<string, string>  $messages
     */
    protected function validationMessage(
        array $data,
        array $rules,
        string $field,
        array $messages = [],
    ): ?string {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return $validator->errors()->first($field);
        }

        return null;
    }

    protected function activeUniqueRule(string $table, string $column): Unique
    {
        return Rule::unique($table, $column)->where(
            fn (QueryBuilder $query): QueryBuilder => $query->whereNull('deleted_at'),
        );
    }

    protected function confirmsOrCancels(string $confirmationLabel, string $cancellationMessage): bool
    {
        if (confirm(label: $confirmationLabel)) {
            return true;
        }

        warning($cancellationMessage);

        return false;
    }

    protected function presentValue(?string $value, string $fallback = 'None'): string
    {
        return filled($value) ? $value : $fallback;
    }
}
