<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface AuthRepositoryInterface
{
    /**
     * Create a new user with hashed password.
     */
    public function createUser(string $name, string $email, string $password): User;
}

