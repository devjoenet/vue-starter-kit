<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

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
}
