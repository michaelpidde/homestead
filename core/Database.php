<?php declare(strict_types=1);

namespace Homestead\Core;

use \PDO;

final class Database {
    static function getConnection() {
        // TODO: Get host and dbname from config. Somehow get username and password (settings.local.ini or getenv or something)
        return new PDO('mysql:host=localhost;dbname=homestead', 'root', 'password', [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}