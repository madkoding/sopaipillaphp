<?php

declare(strict_types=1);

namespace App\Users\DTO;

use Sopaipilla\Validation\Dto;

/**
 * DTO for POST /api/users.
 *
 * Validates that name and email are present and well-formed,
 * then normalizes both before passing them to the model.
 */
final class CreateUserDTO extends Dto
{
    public string $name;
    public string $email;

    /** Validation rules: name is required/min:2/max:100; email is required and valid. */
    protected static function rules(): array
    {
        return [
            'name'  => ['required' => true, 'min' => 2, 'max' => 100],
            'email' => ['required' => true, 'email' => true, 'max' => 255],
        ];
    }

    /** Trim whitespace from name and lowercase the email before storing. */
    protected static function build(array $data): static
    {
        $dto = new static();
        $dto->name  = trim($data['name']);
        $dto->email = strtolower(trim($data['email']));
        return $dto;
    }
}
