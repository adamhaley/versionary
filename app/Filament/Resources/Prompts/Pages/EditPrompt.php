<?php

namespace App\Filament\Resources\Prompts\Pages;

use App\EventSourcing\Aggregates\PromptAggregate;
use App\Filament\Resources\Prompts\PromptResource;
use App\Models\Prompt;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPrompt extends EditRecord
{
    protected static string $resource = PromptResource::class;

    public function getTitle(): string
    {
        return 'Add New Version';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->label('View Prompt')
                ->url(fn () => PromptResource::getUrl('view', ['record' => $this->record]))
                ->color('gray'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Save New Version');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Prompt $prompt */
        $prompt = $this->record;
        $currentVersion = $prompt->currentVersion;

        if ($currentVersion) {
            $data['title'] = $currentVersion->title;
            $data['system_prompt'] = $currentVersion->system_prompt;
            $data['user_prompt_template'] = $currentVersion->user_prompt_template;
            $data['developer_prompt'] = $currentVersion->developer_prompt;
            $data['notes'] = null;
            $data['_branch'] = $currentVersion->branch_name;
        }

        $data['categories'] = $prompt->categories
            ->pluck('category')
            ->map(fn ($c) => is_string($c) ? $c : $c->value)
            ->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Prompt $prompt */
        $prompt = $record;
        $userId = auth()->id();

        $branchName = $data['_branch'] ?? $prompt->currentVersion?->branch_name ?? 'main';

        $latestOnBranch = $prompt->versions()
            ->where('branch_name', $branchName)
            ->latest('created_at')
            ->first();

        $parentVersionId = $latestOnBranch?->id ?? $prompt->current_version_id;

        PromptAggregate::retrieve($prompt->id)
            ->addVersion(
                branchName: $branchName,
                parentVersionId: $parentVersionId,
                createdBy: $userId,
                title: $data['title'] ?? null,
                systemPrompt: $data['system_prompt'] ?? null,
                userPromptTemplate: $data['user_prompt_template'] ?? null,
                developerPrompt: $data['developer_prompt'] ?? null,
                notes: $data['notes'] ?? null,
            )
            ->persist();

        if ($data['name'] !== $prompt->name || $data['slug'] !== $prompt->slug) {
            PromptAggregate::retrieve($prompt->id)
                ->updateMetadata(
                    name: $data['name'],
                    slug: $data['slug'],
                    summary: $data['summary'] ?? null,
                    categories: $data['categories'] ?? [],
                    updatedBy: $userId,
                )
                ->persist();
        }

        Notification::make()
            ->title('New version saved')
            ->success()
            ->send();

        return $prompt->fresh();
    }

    protected function getRedirectUrl(): string
    {
        return PromptResource::getUrl('view', ['record' => $this->record]);
    }
}
