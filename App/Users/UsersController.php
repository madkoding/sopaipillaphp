<?php

declare(strict_types=1);

namespace App\Users;

use Sopaipilla\Routing\Attributes\{Get, Post, Put, Patch, Delete};
use Sopaipilla\Http\ApiController;
use App\Users\DTO\CreateUserDTO;
use App\Users\DTO\UpdateUserDTO;
use App\Users\DTO\ChangePasswordDTO;

/**
 * CRUD controller for the /api/users resource.
 *
 * Every public method annotated with a routing attribute (#[Get], #[Post], etc.)
 * is automatically registered by the Router.
 */
class UsersController extends ApiController
{
    /** Ensure the table exists on every request (safe with CREATE TABLE IF NOT EXISTS). */
    public function __construct()
    {
        parent::__construct();
        UsersModel::migrate();
    }

    /** List all users with a total count in the meta field. */
    #[Get('/api/users')]
    public function index()
    {
        $data = UsersModel::all();
        return $this->json(['data' => $data, 'meta' => ['total' => count($data)]]);
    }

    /** Return a single user by ID or 404 if not found. */
    #[Get('/api/users/{id}')]
    public function show($id)
    {
        return $this->okOr404(UsersModel::find((int) $id), 'User not found');
    }

    /** Create a new user after validating the payload with CreateUserDTO. */
    #[Post('/api/users')]
    public function store()
    {
        return $this->withDto(CreateUserDTO::class,
            fn($dto) => $this->okOr201(UsersModel::create($dto->toArray()))
        );
    }

    /** Replace an existing user's fields (partial updates via UpdateUserDTO). */
    #[Put('/api/users/{id}')]
    public function update($id)
    {
        return $this->withDto(UpdateUserDTO::class,
            fn($dto) => $this->okOr404(UsersModel::update((int) $id, $dto->toArray()), 'User not found')
        );
    }

    /**
     * Return an enriched view of the user with computed display fields.
     * Demonstrates the fetch → transform → respond pattern.
     */
    #[Get('/api/users/{id}/profile')]
    public function profile($id)
    {
        $user = UsersModel::find((int) $id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $profile = [
            ...$user,
            'initials'     => $this->initials($user['name']),
            'masked_email' => $this->maskEmail($user['email']),
        ];

        return $this->json(['data' => $profile]);
    }

    /** Delete a user by ID; returns 404 if the record did not exist. */
    #[Delete('/api/users/{id}')]
    public function destroy($id)
    {
        return $this->okOr404(UsersModel::delete((int) $id), 'User not found');
    }

    /**
     * Example of an endpoint with more complex business logic:
     *   1. Pre-fetch before DTO  → needed to verify existence
     *   2. DTO validates structure → format, minimum length, confirmation
     *   3. Business logic        → verify current password against hash
     *   4. Specific model method → changePassword() instead of generic update()
     */
    #[Patch('/api/users/{id}/password')]
    public function changePassword($id)
    {
        $user = UsersModel::find((int) $id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->withDto(ChangePasswordDTO::class, function ($dto) use ($user) {
            if (!password_verify($dto->currentPassword, $user['password_hash'] ?? '')) {
                return $this->error('Wrong current password', 422);
            }

            UsersModel::changePassword($user['id'], $dto->newPassword);

            return $this->json(['message' => 'Password updated']);
        });
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Extracts the first letter of each word and uppercases it.
     * Example: 'John Doe' → 'JD'
     */
    private function initials(string $name): string
    {
        return implode('', array_map(
            fn($word) => mb_strtoupper(mb_substr($word, 0, 1)),
            explode(' ', trim($name))
        ));
    }

    /**
     * Masks the local part of an email, keeping only the first two characters.
     * Example: 'johndoe@gmail.com' → 'jo*****@gmail.com'
     */
    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $visible = mb_substr($local, 0, 2);
        return $visible . str_repeat('*', max(0, mb_strlen($local) - 2)) . '@' . $domain;
    }
}

