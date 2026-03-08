<?php

namespace App\EventSourcing\Aggregates;

use App\Enums\PromptStatus;
use App\EventSourcing\Events\PromptArchived;
use App\EventSourcing\Events\PromptBranchCreated;
use App\EventSourcing\Events\PromptCreated;
use App\EventSourcing\Events\PromptMetadataUpdated;
use App\EventSourcing\Events\PromptReactivated;
use App\EventSourcing\Events\PromptVersionCreated;
use App\EventSourcing\Events\PromptVersionRestored;
use Illuminate\Support\Str;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class PromptAggregate extends AggregateRoot
{
    private PromptStatus $status = PromptStatus::Draft;

    private int $nextVersionNumber = 1;

    /** @var array<string, int> branch => next version number */
    private array $branchVersionCounters = [];

    /** @var array<string> */
    private array $existingBranchNames = [];

    public function create(
        string $name,
        string $slug,
        ?string $summary,
        array $categories,
        int $createdBy,
        ?string $title = null,
        ?string $systemPrompt = null,
        ?string $userPromptTemplate = null,
        ?string $developerPrompt = null,
        ?string $notes = null,
    ): static {
        $initialVersionId = (string) Str::uuid();

        $this->recordThat(new PromptCreated(
            name: $name,
            slug: $slug,
            summary: $summary,
            categories: $categories,
            createdBy: $createdBy,
            initialVersionId: $initialVersionId,
            initialTitle: $title ?? $name,
            systemPrompt: $systemPrompt,
            userPromptTemplate: $userPromptTemplate,
            developerPrompt: $developerPrompt,
            notes: $notes,
        ));

        return $this;
    }

    public function addVersion(
        string $branchName,
        ?string $parentVersionId,
        int $createdBy,
        ?string $title = null,
        ?string $systemPrompt = null,
        ?string $userPromptTemplate = null,
        ?string $developerPrompt = null,
        ?string $notes = null,
    ): static {
        $versionNumber = $this->branchVersionCounters[$branchName] ?? 1;

        $this->recordThat(new PromptVersionCreated(
            versionId: (string) Str::uuid(),
            versionNumber: $versionNumber,
            branchName: $branchName,
            parentVersionId: $parentVersionId,
            title: $title,
            systemPrompt: $systemPrompt,
            userPromptTemplate: $userPromptTemplate,
            developerPrompt: $developerPrompt,
            notes: $notes,
            createdBy: $createdBy,
        ));

        return $this;
    }

    public function restoreVersion(
        string $fromVersionId,
        string $branchName,
        ?string $parentVersionId,
        int $restoredBy,
        ?string $title = null,
        ?string $systemPrompt = null,
        ?string $userPromptTemplate = null,
        ?string $developerPrompt = null,
    ): static {
        $versionNumber = $this->branchVersionCounters[$branchName] ?? 1;

        $this->recordThat(new PromptVersionRestored(
            newVersionId: (string) Str::uuid(),
            newVersionNumber: $versionNumber,
            branchName: $branchName,
            restoredFromVersionId: $fromVersionId,
            title: $title,
            systemPrompt: $systemPrompt,
            userPromptTemplate: $userPromptTemplate,
            developerPrompt: $developerPrompt,
            notes: "Restored from version {$versionNumber}",
            restoredBy: $restoredBy,
        ));

        return $this;
    }

    public function createBranch(
        string $branchName,
        string $baseVersionId,
        int $createdBy,
    ): static {
        if (in_array($branchName, $this->existingBranchNames, true)) {
            throw new \RuntimeException("Branch '{$branchName}' already exists on this prompt.");
        }

        $this->recordThat(new PromptBranchCreated(
            branchName: $branchName,
            baseVersionId: $baseVersionId,
            createdBy: $createdBy,
        ));

        return $this;
    }

    public function updateMetadata(
        string $name,
        string $slug,
        ?string $summary,
        array $categories,
        int $updatedBy,
    ): static {
        $this->recordThat(new PromptMetadataUpdated(
            name: $name,
            slug: $slug,
            summary: $summary,
            categories: $categories,
            updatedBy: $updatedBy,
        ));

        return $this;
    }

    public function archive(int $archivedBy): static
    {
        if ($this->status === PromptStatus::Archived) {
            return $this;
        }

        $this->recordThat(new PromptArchived(archivedBy: $archivedBy));

        return $this;
    }

    public function reactivate(int $reactivatedBy): static
    {
        if ($this->status !== PromptStatus::Archived) {
            return $this;
        }

        $this->recordThat(new PromptReactivated(reactivatedBy: $reactivatedBy));

        return $this;
    }

    protected function applyPromptCreated(PromptCreated $event): void
    {
        $this->status = PromptStatus::Draft;
        $this->existingBranchNames[] = 'main';
        $this->branchVersionCounters['main'] = 2;
    }

    protected function applyPromptVersionCreated(PromptVersionCreated $event): void
    {
        $this->branchVersionCounters[$event->branchName] = ($this->branchVersionCounters[$event->branchName] ?? 0) + 1;
    }

    protected function applyPromptVersionRestored(PromptVersionRestored $event): void
    {
        $this->branchVersionCounters[$event->branchName] = ($this->branchVersionCounters[$event->branchName] ?? 0) + 1;
    }

    protected function applyPromptBranchCreated(PromptBranchCreated $event): void
    {
        $this->existingBranchNames[] = $event->branchName;
        $this->branchVersionCounters[$event->branchName] = 1;
    }

    protected function applyPromptArchived(PromptArchived $event): void
    {
        $this->status = PromptStatus::Archived;
    }

    protected function applyPromptReactivated(PromptReactivated $event): void
    {
        $this->status = PromptStatus::Active;
    }
}
