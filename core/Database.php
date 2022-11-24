<?php declare(strict_types=1);

namespace Homestead\Core;

use \PDO;

final class Database {
    public static function getConnection() {
        return new PDO('mysql:host=localhost;dbname=homestead', getenv('HomesteadDbUser'), getenv('HomesteadDbPassword'), [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}