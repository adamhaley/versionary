<?php

namespace App\EventSourcing\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PromptMetadataUpdated extends ShouldBeStored
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $summary,
        public readonly array $categories,
        public readonly int $updatedBy,
    ) {}
}
