<?php

declare(strict_types=1);

namespace Sopaipilla\Validation;

class ValidationException extends \RuntimeException
{
    public function __construct(private readonly array $errors)
    {
        parent::__construct('Validation failed');
    }

    /** Todos los errores indexados por campo */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /** Primer mensaje de error de todos los campos */
    public function firstMessage(): string
    {
        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }
        return 'Validation failed';
    }

    /** Todos los mensajes aplanados */
    public function allMessages(): array
    {
        return array_merge(...array_values($this->errors));
    }
}
