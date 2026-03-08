<?php

namespace App\Filament\Resources\Prompts\Pages;

use App\EventSourcing\Aggregates\PromptAggregate;
use App\Filament\Resources\Prompts\PromptResource;
use App\Models\Prompt;
use Filament\Actions\Action;
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
        }

        $data['categories'] = $prompt->categories->pluck('category')->map(fn ($c) => is_string($c) ? $c : $c->value)->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Prompt $prompt */
        $prompt = $record;
        $userId = auth()->id();

        $currentVersionId = $prompt->current_version_id;

        PromptAggregate::retrieve($prompt->id)
            ->addVersion(
                branchName: $prompt->currentVersion?->branch_name ?? 'main',
                parentVersionId: $currentVersionId,
                createdBy: $userId,
                title: $data['title'] ?? null,
                systemPrompt: $data['system_prompt'] ?? null,
                userPromptTemplate: $data['user_prompt_template'] ?? null,
                developerPrompt: $data['developer_prompt'] ?? null,
                notes: $data['notes'] ?? null,
            )
            ->persist();

        if (! empty($data['name']) && $data['name'] !== $prompt->name) {
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

        return $prompt->fresh();
    }

    protected function getRedirectUrl(): string
    {
        return PromptResource::getUrl('view', ['record' => $this->record]);
    }
}
