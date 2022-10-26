<?php declare(strict_types=1);

namespace Homestead\Core;

use \Exception;

final class Session {
    private static ?Session $instance = null;
    private static ?Database $database = null;

    private function __construct() {}

    static function getInstance(): Session {
        if(self::$instance === null) {
            self::$instance = new Session();
        }
        self::$database = new Database();
        return self::$instance;
    }

    static function add(int $userId, string $name, mixed $data): void {
        try {
            $handle = self::$database->getConnection();
            $statement = $handle->prepare('insert into session (userId, name, data) values (:userid, :name, :data)');
            $statement->execute([
                'userid' => $userId,
                'name' => $name,
                'data' => json_encode($data),
            ]);
        } catch(PDOException $e) {
            throw new SessionException($e->getMessage());
        }
    }

    static function get(int $userId, string $name): mixed {
        try {
            $handle = self::$database->getConnection();
            $statement = $handle->prepare('select data from session where userid = :userid and name = :name');
            if(!$statement->execute([
                'userid' => $userId,
                'name' => $name,
            ])) {
                return null;
            }
            $data = $statement->fetch();
            if(!$data) {
                return null;
            }
            return json_decode($data['data']);
        } catch(PDOException $e) {
            throw new SessionException($e->getMessage());
        }
    }
}

class SessionException extends Exception {}