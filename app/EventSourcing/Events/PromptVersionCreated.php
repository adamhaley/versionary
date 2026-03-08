<?php

namespace App\EventSourcing\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PromptVersionCreated extends ShouldBeStored
{
    public function __construct(
        public readonly string $versionId,
        public readonly int $versionNumber,
        public readonly string $branchName,
        public readonly ?string $parentVersionId,
        public readonly ?string $title,
        public readonly ?string $systemPrompt,
        public readonly ?string $userPromptTemplate,
        public readonly ?string $developerPrompt,
        public readonly ?string $notes,
        public readonly int $createdBy,
    ) {}
}
