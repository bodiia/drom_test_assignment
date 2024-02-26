<?php

namespace Bodianskii\Task2\Model;

final readonly class Comment
{
    public function __construct(
        private string $name,
        private string $text,
        private ?int $id = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public static function fromResponseData(array $data): self
    {
        return new self(
            $data['name'],
            $data['text'],
            $data['id']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'text' => $this->text,
        ];
    }
}