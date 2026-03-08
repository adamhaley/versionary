<?php

namespace App\EventSourcing\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PromptArchived extends ShouldBeStored
{
    public function __construct(
        public readonly int $archivedBy,
    ) {}
}
