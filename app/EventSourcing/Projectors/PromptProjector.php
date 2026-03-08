<?php

namespace App\EventSourcing\Projectors;

use App\Enums\PromptStatus;
use App\EventSourcing\Events\PromptArchived;
use App\EventSourcing\Events\PromptBranchCreated;
use App\EventSourcing\Events\PromptCreated;
use App\EventSourcing\Events\PromptMetadataUpdated;
use App\EventSourcing\Events\PromptReactivated;
use App\EventSourcing\Events\PromptVersionCreated;
use App\EventSourcing\Events\PromptVersionRestored;
use App\Models\Prompt;
use App\Models\PromptBranch;
use App\Models\PromptCategory;
use App\Models\PromptVersion;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PromptProjector extends Projector
{
    public function onPromptCreated(PromptCreated $event, string $aggregateUuid): void
    {
        $prompt = Prompt::create([
            'id' => $aggregateUuid,
            'name' => $event->name,
            'slug' => $event->slug,
            'summary' => $event->summary,
            'status' => PromptStatus::Draft,
            'current_version_id' => $event->initialVersionId,
            'created_by' => $event->createdBy,
            'updated_by' => $event->createdBy,
        ]);

        foreach ($event->categories as $category) {
            PromptCategory::create([
                'prompt_id' => $aggregateUuid,
                'category' => $category,
            ]);
        }

        PromptVersion::create([
            'id' => $event->initialVersionId,
            'prompt_id' => $aggregateUuid,
            'version_number' => 1,
            'branch_name' => 'main',
            'parent_version_id' => null,
            'title' => $event->initialTitle,
            'system_prompt' => $event->systemPrompt,
            'user_prompt_template' => $event->userPromptTemplate,
            'developer_prompt' => $event->developerPrompt,
            'notes' => $event->notes ?? 'Initial version',
            'is_restored' => false,
            'created_by' => $event->createdBy,
            'created_at' => now(),
        ]);

        PromptBranch::create([
            'prompt_id' => $aggregateUuid,
            'name' => 'main',
            'base_version_id' => $event->initialVersionId,
            'created_by' => $event->createdBy,
        ]);
    }

    public function onPromptVersionCreated(PromptVersionCreated $event, string $aggregateUuid): void
    {
        PromptVersion::create([
            'id' => $event->versionId,
            'prompt_id' => $aggregateUuid,
            'version_number' => $event->versionNumber,
            'branch_name' => $event->branchName,
            'parent_version_id' => $event->parentVersionId,
            'title' => $event->title,
            'system_prompt' => $event->systemPrompt,
            'user_prompt_template' => $event->userPromptTemplate,
            'developer_prompt' => $event->developerPrompt,
            'notes' => $event->notes,
            'is_restored' => false,
            'created_by' => $event->createdBy,
            'created_at' => now(),
        ]);

        Prompt::where('id', $aggregateUuid)->update([
            'current_version_id' => $event->versionId,
            'updated_by' => $event->createdBy,
        ]);
    }

    public function onPromptVersionRestored(PromptVersionRestored $event, string $aggregateUuid): void
    {
        PromptVersion::create([
            'id' => $event->newVersionId,
            'prompt_id' => $aggregateUuid,
            'version_number' => $event->newVersionNumber,
            'branch_name' => $event->branchName,
            'parent_version_id' => $event->restoredFromVersionId,
            'title' => $event->title,
            'system_prompt' => $event->systemPrompt,
            'user_prompt_template' => $event->userPromptTemplate,
            'developer_prompt' => $event->developerPrompt,
            'notes' => $event->notes,
            'is_restored' => true,
            'restored_from_version_id' => $event->restoredFromVersionId,
            'created_by' => $event->restoredBy,
            'created_at' => now(),
        ]);

        Prompt::where('id', $aggregateUuid)->update([
            'current_version_id' => $event->newVersionId,
            'updated_by' => $event->restoredBy,
        ]);
    }

    public function onPromptBranchCreated(PromptBranchCreated $event, string $aggregateUuid): void
    {
        PromptBranch::create([
            'prompt_id' => $aggregateUuid,
            'name' => $event->branchName,
            'base_version_id' => $event->baseVersionId,
            'created_by' => $event->createdBy,
        ]);
    }

    public function onPromptMetadataUpdated(PromptMetadataUpdated $event, string $aggregateUuid): void
    {
        Prompt::where('id', $aggregateUuid)->update([
            'name' => $event->name,
            'slug' => $event->slug,
            'summary' => $event->summary,
            'updated_by' => $event->updatedBy,
        ]);

        PromptCategory::where('prompt_id', $aggregateUuid)->delete();

        foreach ($event->categories as $category) {
            PromptCategory::create([
                'prompt_id' => $aggregateUuid,
                'category' => $category,
            ]);
        }
    }

    public function onPromptArchived(PromptArchived $event, string $aggregateUuid): void
    {
        Prompt::where('id', $aggregateUuid)->update([
            'status' => PromptStatus::Archived,
            'updated_by' => $event->archivedBy,
        ]);
    }

    public function onPromptReactivated(PromptReactivated $event, string $aggregateUuid): void
    {
        Prompt::where('id', $aggregateUuid)->update([
            'status' => PromptStatus::Active,
            'updated_by' => $event->reactivatedBy,
        ]);
    }
}
