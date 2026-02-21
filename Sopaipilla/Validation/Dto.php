<?php

declare(strict_types=1);

namespace Sopaipilla\Validation;

abstract class Dto
{
    /**
     * Define the validation rules for this DTO.
     * Uses the same format as Validator: ['field' => ['rule' => param]]
     */
    abstract protected static function rules(): array;

    /**
     * Builds the DTO from an already-validated array.
     * Subclasses assign their properties here.
     */
    abstract protected static function build(array $data): static;

    /**
     * Validates $data against the DTO rules and returns an instance.
     * Throws ValidationException if validation fails.
     *
     * @throws ValidationException
     */
    public static function from(array $data): static
    {
        $validator = new Validator($data, static::rules());

        if (!$validator->validate()) {
            throw new ValidationException($validator->getErrors());
        }

        return static::build($data);
    }

    /**
     * Like from(), but returns null instead of throwing an exception.
     * Errors are accessible via $errors (passed by reference).
     */
    public static function tryFrom(array $data, ?array &$errors = null): ?static
    {
        try {
            return static::from($data);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            return null;
        }
    }

    /**
     * Exports the DTO properties as an array.
     * Only includes public initialized properties (excludes optional nulls).
     */
    public function toArray(): array
    {
        return array_filter(
            get_object_vars($this),
            fn ($v) => $v !== null
        );
    }
}
