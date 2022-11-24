<?php declare(strict_types=1);

namespace Clozerwoods\Model;

use \DateTime;

class Page {
    public function __construct(
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

    public function id(): int {
        return $this->id;
    }

    public function parentId(): ?int {
        return $this->parentId;
    }

    public function stub(): string {
        return $this->stub;
    }

    public function title(): string {
        return $this->title;
    }

    public function content(): string {
        return $this->content;
    }

    public function isHome(): bool {
        return $this->isHome;
    }

    public function published(): bool {
        return $this->published;
    }

    public function created(): DateTime {
        return $this->created;
    }

    public function updated(): ?DateTime {
        return $this->updated;
    }

    public function _children(array $value) {
        $this->children = $value;
    }

    public function children(): array {
        return $this->children;
    }

    public static function new(array $record): self {
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