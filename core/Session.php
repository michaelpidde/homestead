<?php declare(strict_types=1);

namespace Homestead\Core;

use \DateTimeImmutable;
use \Exception;

final class Session {
    const COOKIE = 'HOMESTEAD_SESSION';
    private static ?Session $instance = null;
    private static ?Database $database = null;

    private function __construct() {}

    static function getInstance(): Session {
        if(self::$instance === null) {
            self::$instance = new Session();
        }
        self::$database = new Database();
        self::createTable();
        return self::$instance;
    }

    private static function createTable(): void {
        $sql = <<<SQL
            create table if not exists `session` (
                id varchar(50) not null primary key,
                name varchar(100) not null,
                data json not null,
                created datetime not null default now(),
                unique key session_id (id, name)
            )
            SQL;

        try {
            $handle = self::$database->getConnection();
            $handle->query($sql);
        }  catch(PDOException $e) {
            throw new SessionException($e->getMessage());
        }
    }

    static function id(): string|false {
        if(!array_key_exists(self::COOKIE, $_COOKIE)) {
            return false;
        }
        return $_COOKIE[self::COOKIE];
    }

    static function start(): string {
        $id = self::id();
        if(!$id) {
            $timestamp = (string)(new DateTimeImmutable())->getTimestamp();
            $uniqueId = str_replace('.', '_', uniqid($timestamp, true));
            setcookie(
                self::COOKIE,
                $uniqueId,
                [
                    'httponly' => true,
                    'domain' => 'localhost',
                    'path' => '/',
                ]
            );
            return $uniqueId;
        }
        return $id;
    }

    static function end(): void {
        $id = self::id();
        try {
            $handle = self::$database->getConnection();
            $statement = $handle->prepare('delete from session where id = :id');
            $statement->execute([
                'id' => $id,
            ]);
        } catch(PDOException $e) {
            throw new SessionException($e->getMessage());
        }
        $_COOKIE[self::COOKIE] = null;
        setcookie(self::COOKIE, '', time() - 3600, '/');
    }

    static function add(string $id, string $name, mixed $data): void {
        try {
            $handle = self::$database->getConnection();
            $statement = $handle->prepare('insert into session (id, name, data) values (:id, :name, :data) on duplicate key update data = :data');
            $statement->execute([
                'id' => $id,
                'name' => $name,
                'data' => json_encode($data),
            ]);
        } catch(PDOException $e) {
            throw new SessionException($e->getMessage());
        }
    }

    static function get(string $id, string $name): mixed {
        try {
            $handle = self::$database->getConnection();
            $statement = $handle->prepare('select data from session where id = :id and name = :name');
            if(!$statement->execute([
                'id' => $id,
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

    static function delete(string $id, string $name): void {
        try {
            $handle = self::$database->getConnection();
            $statement = $handle->query('delete from session  where id = :id and name = :name');
            $statement->execute([
                'id' => $id,
                'name' => $name,
            ]);
        } catch(PDOException $e) {
            throw new SessionException($e->getMessage());
        }
    }
}

class SessionException extends Exception {}