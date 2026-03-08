<?php

namespace App\EventSourcing\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PromptVersionRestored extends ShouldBeStored
{
    public function __construct(
        public readonly string $newVersionId,
        public readonly int $newVersionNumber,
        public readonly string $branchName,
        public readonly string $restoredFromVersionId,
        public readonly ?string $title,
        public readonly ?string $systemPrompt,
        public readonly ?string $userPromptTemplate,
        public readonly ?string $developerPrompt,
        public readonly ?string $notes,
        public readonly int $restoredBy,
    ) {}
}
