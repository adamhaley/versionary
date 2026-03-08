<?php

namespace App\EventSourcing\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PromptCreated extends ShouldBeStored
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $summary,
        public readonly array $categories,
        public readonly int $createdBy,
        public readonly string $initialVersionId,
        public readonly string $initialTitle,
        public readonly ?string $systemPrompt,
        public readonly ?string $userPromptTemplate,
        public readonly ?string $developerPrompt,
        public readonly ?string $notes,
    ) {}
}
