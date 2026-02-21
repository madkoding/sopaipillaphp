<?php

declare(strict_types=1);

namespace App\Users\DTO;

use Sopaipilla\Validation\Dto;

/**
 * DTO for PUT /api/users/{id}.
 *
 * All fields are optional — only the ones present in the payload are updated.
 * toArray() automatically excludes null properties, so the model only receives
 * the fields the client actually sent.
 */
final class UpdateUserDTO extends Dto
{
    public ?string $name  = null; // null means "not provided by the client"
    public ?string $email = null; // null means "not provided by the client"

    /** All rules are optional (no 'required'). Validation only runs when the field is present. */
    protected static function rules(): array
    {
        return [
            'name'  => ['min' => 2, 'max' => 100],
            'email' => ['email' => true, 'max' => 255],
        ];
    }

    /** Only set properties that were actually sent; leave others null so toArray() omits them. */
    protected static function build(array $data): static
    {
        $dto = new static();

        if (isset($data['name'])) {
            $dto->name = trim($data['name']);
        }

        if (isset($data['email'])) {
            $dto->email = strtolower(trim($data['email']));
        }

        return $dto;
    }
}
