<?php

namespace App\EventSourcing\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PromptBranchCreated extends ShouldBeStored
{
    public function __construct(
        public readonly string $branchName,
        public readonly string $baseVersionId,
        public readonly int $createdBy,
    ) {}
}
