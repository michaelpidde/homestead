<?php declare(strict_types=1);

namespace Homestead\Core;

interface AuthenticationInterface {
    public function authenticate(string $username, string $password): bool;
    public function isAuthenticated(): bool;
    public function loginRoute(): string;
    public function postLoginRoute(): string;
    public function logout(): void;
}