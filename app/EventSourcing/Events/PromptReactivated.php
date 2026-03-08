<?php

namespace App\EventSourcing\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PromptReactivated extends ShouldBeStored
{
    public function __construct(
        public readonly int $reactivatedBy,
    ) {}
}
