<?php declare(strict_types=1);

namespace Clozerwoods\Service;

use Homestead\Core\AuthenticationInterface;
use Homestead\Core\Database;
use Homestead\Core\Session;

class AuthenticationService implements AuthenticationInterface {
    const AUTH_COOKIE_NAME = 'CLOZER_AUTH';

    function __construct(private Database $database, private Session $session) { }

    function loginRoute(): string {
        return 'maingate/login';
    }

    function postLoginRoute(): string {
        return 'maingate/dashboard';
    }

    function authenticate(string $username, string $password): bool {
        try {
            $handle = $this->database->getConnection();
            $statement = $handle->prepare('select id from user where email = :email and lower(hex(password)) = :password');
            $hash = hash('sha256', $password);
            if(!$statement->execute([
                'email' => $username,
                'password' => $hash,
            ])) {
                return false;
            }
            $user = $statement->fetch();
            if($user !== false) {
                $userId = $user['id'];
                $sessionId = Session::id();
                $authData = json_encode(['sessionId' => $sessionId, 'userid' => $userId]);
                $this->session::add($sessionId, self::AUTH_COOKIE_NAME, $authData);
                setcookie(
                    self::AUTH_COOKIE_NAME,
                    $authData,
                    [
                        'httponly' => true,
                        'domain' => getenv('HomesteadDomain'),
                        'path' => '/',
                    ]
                );
                return true;
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    function isAuthenticated(): bool {
        $sessionId = Session::id();
        if(!$sessionId) {
            return false;
        }

        if(!array_key_exists(self::AUTH_COOKIE_NAME, $_COOKIE)) {
            return false;
        }
        $cookie = $_COOKIE[self::AUTH_COOKIE_NAME];
        $decodedCookie = json_decode($cookie, true);

        if(!array_key_exists('sessionId', $decodedCookie)) {
            return false;
        }
        
        $sessionAuth = Session::get($decodedCookie['sessionId'], self::AUTH_COOKIE_NAME);
        if($sessionAuth === null) {
            return false;
        }
        
        if($sessionAuth === $cookie) {
            return true;
        }
        
        return false;
    }

    function logout(): void {
        setcookie(self::AUTH_COOKIE_NAME, '', time() - 3600, '/');
        $_COOKIE[self::AUTH_COOKIE_NAME] = null;
        Session::end();
    }
}