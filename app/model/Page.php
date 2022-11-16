<?php declare(strict_types=1);

namespace Clozerwoods\Model;

use \DateTime;

class Page {
    function __construct(
        private int $id,
        private ?int $parentId,
        private string $stub,
        private string $title,
        private string $content,
        private bool $isHome,
        private bool $published,
        private DateTime $created,
        private ?DateTime $updated,
        private array $children = []
    ) {}

    function id(): int {
        return $this->id;
    }

    function parentId(): int {
        return $this->id;
    }

    function stub(): string {
        return $this->stub;
    }

    function title(): string {
        return $this->title;
    }

    function content(): string {
        return $this->content;
    }

    function isHome(): bool {
        return $this->isHome;
    }

    function published(): bool {
        return $this->published;
    }

    function created(): DateTime {
        return $this->created;
    }

    function updated(): ?DateTime {
        return $this->updated;
    }

    function _children(array $value) {
        $this->children = $value;
    }

    function children(): array {
        return $this->children;
    }

    static function new(array $record): self {
        return new self(
            $record['id'],
            $record['parentId'],
            $record['stub'],
            $record['title'],
            $record['content'],
            (bool)$record['isHome'],
            (bool)$record['published'],
            new DateTime($record['created']),
            ($record['updated'] !== null) ? new DateTime($record['updated']) : null
        );
    }
}