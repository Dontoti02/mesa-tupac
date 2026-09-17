<?php
declare(strict_types=1);

namespace App\Helpers;

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data);
        $validator->validate($rules);
        return $validator;
    }

    public function validate(array $rules): void
    {
        foreach ($rules as $field => $fieldRules) {
            $ruleList = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);
            $value = $this->data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                $this->applyRule($field, $value, $ruleName, $params);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule, array $params): void
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    $this->addError($field, "El campo {$label} es obligatorio.");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "El campo {$label} debe ser un correo electrónico válido.");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "El campo {$label} debe ser numérico.");
                }
                break;

            case 'dni':
                if (!empty($value) && (!preg_match('/^[0-9]{8}$/', (string)$value))) {
                    $this->addError($field, "El DNI debe contener exactamente 8 dígitos numéricos.");
                }
                break;

            case 'min':
                $min = (int)($params[0] ?? 0);
                if (!empty($value) && strlen((string)$value) < $min) {
                    $this->addError($field, "El campo {$label} debe tener al menos {$min} caracteres.");
                }
                break;

            case 'max':
                $max = (int)($params[0] ?? 0);
                if (!empty($value) && strlen((string)$value) > $max) {
                    $this->addError($field, "El campo {$label} no puede exceder {$max} caracteres.");
                }
                break;

            case 'in':
                if (!empty($value) && !in_array((string)$value, $params, true)) {
                    $this->addError($field, "El valor seleccionado para {$label} no es válido.");
                }
                break;

            case 'same':
                $otherField = $params[0] ?? '';
                $otherValue = $this->data[$otherField] ?? null;
                if ($value !== $otherValue) {
                    $this->addError($field, "El campo {$label} no coincide con {$otherField}.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function firstOfAll(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }
        return null;
    }
}
