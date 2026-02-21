<?php

declare(strict_types=1);

namespace App\Users\DTO;

use Sopaipilla\Validation\Dto;
use Sopaipilla\Validation\ValidationException;

/**
 * DTO for PATCH /api/users/{id}/password.
 *
 * Validates that:
 *   - current_password is present (used to verify identity before changing)
 *   - new_password meets the minimum length requirement
 *   - password_confirmation matches new_password (checked inside build())
 */
class ChangePasswordDTO extends Dto
{
    public function __construct(
        /** The user's current password (plain text, for verification only). */
        public readonly string $currentPassword,
        /** The new password chosen by the user (plain text, will be hashed by the model). */
        public readonly string $newPassword,
    ) {}

    /** current_password and new_password are required; new_password must be at least 8 characters. */
    protected static function rules(): array
    {
        return [
            'current_password'      => ['required' => true],
            'new_password'          => ['required' => true, 'min' => 8],
            'password_confirmation' => ['required' => true],
        ];
    }

    /**
     * Verify the two password fields match before constructing the DTO.
     * Throws ValidationException so the error is returned in the same 422 format.
     */
    protected static function build(array $data): static
    {
        if ($data['new_password'] !== $data['password_confirmation']) {
            throw new ValidationException([
                'password_confirmation' => ['Passwords do not match'],
            ]);
        }

        return new static(
            currentPassword: $data['current_password'],
            newPassword:     $data['new_password'],
        );
    }
}
