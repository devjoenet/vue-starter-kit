<?php

declare(strict_types=1);

namespace App\Support\TypeScript;

use Illuminate\Foundation\Http\FormRequest;
use ReflectionClass;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\Transformers\Transformer;

class FormRequestRulesTransformer implements Transformer
{
    public function transform(ReflectionClass $class, string $name): ?TransformedType
    {
        if (! is_subclass_of($class->getName(), FormRequest::class)) {
            return null;
        }

        if (! $class->hasMethod('rules')) {
            return null;
        }

        /** @var FormRequest $request */
        $request = $class->newInstanceWithoutConstructor();
        $request->setContainer(app());
        $request->setUserResolver(static fn (): object => (object) ['id' => 0]);
        $rulesMethod = $class->getMethod('rules');
        $rulesMethod->setAccessible(true);
        $rules = $rulesMethod->invoke($request);

        if (! is_array($rules)) {
            return null;
        }

        /** @var array<string, array{required: bool, type: string}> $fields */
        $fields = [];

        /** @var array<string, string> $arrayItemTypes */
        $arrayItemTypes = [];

        foreach ($rules as $field => $ruleDefinition) {
            if (! is_string($field)) {
                continue;
            }

            $ruleNames = $this->normalizeRuleNames($ruleDefinition);
            $type = $this->resolveType($ruleNames);
            $required = $this->isRequired($ruleNames);

            if (str_ends_with($field, '.*')) {
                $parentField = mb_substr($field, 0, -2);

                if ($parentField !== '') {
                    $arrayItemTypes[$parentField] = $type;
                }

                continue;
            }

            $fields[$field] = [
                'required' => $required,
                'type' => $type,
            ];
        }

        foreach ($arrayItemTypes as $parentField => $itemType) {
            $existing = $fields[$parentField] ?? [
                'required' => false,
                'type' => 'Array<unknown>',
            ];

            $fields[$parentField] = [
                'required' => $existing['required'],
                'type' => "Array<{$itemType}>",
            ];
        }

        $properties = [];

        foreach ($fields as $field => $definition) {
            $key = preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $field) ? $field : "'{$field}'";
            $properties[] = sprintf('    %s%s: %s;', $key, $definition['required'] ? '' : '?', $definition['type']);
        }

        $transformed = empty($properties)
            ? 'Record<string, never>'
            : "{\n".implode("\n", $properties)."\n}";

        return TransformedType::create(
            $class,
            $name,
            $transformed
        );
    }

    /** @return array<int, string> */
    private function normalizeRuleNames(mixed $ruleDefinition): array
    {
        if (is_string($ruleDefinition)) {
            return array_filter(array_map(
                fn (string $rule): string => mb_trim(mb_strtolower($rule)),
                explode('|', $ruleDefinition)
            ));
        }

        if (! is_array($ruleDefinition)) {
            return [];
        }

        $normalized = [];

        foreach ($ruleDefinition as $rule) {
            if (is_string($rule)) {
                $normalized[] = mb_trim(mb_strtolower($rule));

                continue;
            }

            if (is_object($rule) && method_exists($rule, '__toString')) {
                $normalized[] = mb_trim(mb_strtolower((string) $rule));
            }
        }

        return array_values(array_filter($normalized));
    }

    /** @param  array<int, string>  $ruleNames */
    private function resolveType(array $ruleNames): string
    {
        if (empty($ruleNames)) {
            return 'unknown';
        }

        foreach ($ruleNames as $rule) {
            if (str_starts_with($rule, 'in:')) {
                $union = $this->resolveInUnion($rule);

                if ($union !== null) {
                    return $union;
                }
            }
        }

        if ($this->containsRule($ruleNames, ['array'])) {
            return 'Array<unknown>';
        }

        if ($this->containsRule($ruleNames, ['bool', 'boolean', 'accepted', 'declined'])) {
            return 'boolean';
        }

        if ($this->containsRule($ruleNames, ['integer', 'numeric', 'decimal'])) {
            return 'number';
        }

        return 'string';
    }

    /** @param  array<int, string>  $ruleNames */
    private function isRequired(array $ruleNames): bool
    {
        if ($this->containsRule($ruleNames, ['sometimes'])) {
            return false;
        }

        if ($this->containsRule($ruleNames, ['nullable'])) {
            return false;
        }

        return $this->containsRule($ruleNames, ['required', 'required_if', 'required_unless', 'required_with', 'required_without', 'present']);
    }

    /**
     * @param  array<int, string>  $ruleNames
     * @param  array<int, string>  $needles
     */
    private function containsRule(array $ruleNames, array $needles): bool
    {
        foreach ($ruleNames as $rule) {
            $ruleName = explode(':', $rule)[0];

            if (in_array($ruleName, $needles, true)) {
                return true;
            }
        }

        return false;
    }

    private function resolveInUnion(string $rule): ?string
    {
        $parts = explode(':', $rule, 2);

        if (count($parts) !== 2 || empty($parts[1])) {
            return null;
        }

        $values = array_filter(array_map('trim', explode(',', $parts[1])));

        if (empty($values)) {
            return null;
        }

        $mapped = array_map(
            static function (string $value): string {
                if (is_numeric($value)) {
                    return $value;
                }

                return "'".str_replace("'", "\\'", $value)."'";
            },
            $values
        );

        return implode(' | ', $mapped);
    }
}
