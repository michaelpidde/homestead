<?php declare(strict_types=1);

namespace Clozerwoods\Service;

use \DateTime;
use \Exception;
use \PDOException;
use Homestead\Core\Database;
use Clozerwoods\Model\Page;

final class MainGateService {
    public function getPage(int $id): Page {
        try {
            $handle = Database::getConnection();
            $statement = $handle->prepare('select id, parentId, stub, title, content, isHome, published, created, updated from page where id = :id');
            if(!$statement->execute([
                'id' => $id,
            ])) {
                // TODO: Throw exception or something...
            }
            $page = $statement->fetch();
            if(!$page) {
                // TODO: Throw exception or something...
            }
            return Page::new($page);
        } catch(PDOException $e) {
            throw new MainGateServiceException($e->getMessage());
        }
    }

    public function getChildPages(int $parentId): array {
        try {
            $handle = Database::getConnection();
            $statement = $handle->prepare('select id, parentId, stub, title, content, isHome, published, created, updated from page where parentId = :id');
            if(!$statement->execute([
                'id' => $parentId,
            ])) {
                // TODO: Throw exception or something...
            }
            $pages = $statement->fetchAll();
            if(!$pages) {
                // TODO: Throw exception or something...
            }
            $result = [];
            foreach($pages as $page) {
                $result[] = Page::new($page);
            }
            return $result;
        } catch(PDOException $e) {
            throw new MainGateServiceException($e->getMessage());
        }
    }

    public function getPages(): array {
        try {
            $handle = Database::getConnection();
            $statement = $handle->prepare('select id, parentId, stub, title, content, isHome, published, created, updated from page');
            if(!$statement->execute()) {
                // TODO: Throw exception or something...
            }
            $pages = $statement->fetchAll();
            if(!$pages) {
                // TODO: Throw exception or something...
            }
            $result = [];
            foreach($pages as $page) {
                $result[] = Page::new($page);
            }
            return $result;
        } catch(PDOException $e) {
            throw new MainGateServiceException($e->getMessage());
        }
    }

    public function getPagesExcept(int $id): array {
        try {
            $handle = Database::getConnection();
            $statement = $handle->prepare('select id, parentId, stub, title, content, isHome, published, created, updated from page where id != :id');
            if(!$statement->execute(['id' => $id])) {
                // TODO: Throw exception or something...
            }
            $pages = $statement->fetchAll();
            if(!$pages) {
                // TODO: Throw exception or something...
            }
            $result = [];
            foreach($pages as $page) {
                $result[] = Page::new($page);
            }
            return $result;
        } catch(PDOException $e) {
            throw new MainGateServiceException($e->getMessage());
        }
    }
}

class MainGateServiceException extends Exception {}