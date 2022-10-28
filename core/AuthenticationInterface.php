<?php declare(strict_types=1);

namespace Homestead\Core;

interface AuthenticationInterface {
    function authenticate(string $username, string $password): bool;
    function isAuthenticated(): bool;
    function loginRoute(): string;
    function postLoginRoute(): string;
    function logout(): void;
}