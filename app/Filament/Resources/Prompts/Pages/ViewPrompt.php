<?php

namespace App\Filament\Resources\Prompts\Pages;

use App\Enums\PromptStatus;
use App\EventSourcing\Aggregates\PromptAggregate;
use App\Filament\Resources\Prompts\PromptResource;
use App\Models\Prompt;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewPrompt extends ViewRecord
{
    protected static string $resource = PromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('new_version')
                ->label('New Version')
                ->icon('heroicon-o-plus')
                ->url(fn () => PromptResource::getUrl('edit', ['record' => $this->record])),

            Action::make('archive')
                ->label('Archive')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status !== PromptStatus::Archived)
                ->action(function () {
                    /** @var Prompt $prompt */
                    $prompt = $this->record;
                    PromptAggregate::retrieve($prompt->id)
                        ->archive(auth()->id())
                        ->persist();
                    $this->refreshFormData(['status']);
                }),

            Action::make('reactivate')
                ->label('Reactivate')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === PromptStatus::Archived)
                ->action(function () {
                    /** @var Prompt $prompt */
                    $prompt = $this->record;
                    PromptAggregate::retrieve($prompt->id)
                        ->reactivate(auth()->id())
                        ->persist();
                    $this->refreshFormData(['status']);
                }),

            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Prompt Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (PromptStatus $state): string => match ($state) {
                                PromptStatus::Draft => 'gray',
                                PromptStatus::Active => 'success',
                                PromptStatus::Archived => 'danger',
                            }),
                        TextEntry::make('updater.name')->label('Last Edited By'),
                        TextEntry::make('summary')->columnSpanFull(),
                    ]),

                Section::make('Branches')
                    ->columns(1)
                    ->schema([
                        RepeatableEntry::make('branches')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')->label('Branch')->badge()->color('info'),
                                TextEntry::make('baseVersion.version_number')
                                    ->label('Based On')
                                    ->prefix('v'),
                                TextEntry::make('creator.name')->label('Created By'),
                                TextEntry::make('created_at')->label('Created')->since(),
                            ])
                            ->columns(4),
                    ]),

                Section::make('Current Version')
                    ->schema([
                        TextEntry::make('currentVersion.branch_name')
                            ->label('Branch')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('currentVersion.version_number')
                            ->label('Version')
                            ->prefix('v'),
                        TextEntry::make('currentVersion.title')
                            ->label('Title'),
                        TextEntry::make('currentVersion.notes')
                            ->label('Notes'),
                        TextEntry::make('currentVersion.system_prompt')
                            ->label('System Prompt')
                            ->prose()
                            ->columnSpanFull(),
                        TextEntry::make('currentVersion.user_prompt_template')
                            ->label('User Prompt Template')
                            ->prose()
                            ->columnSpanFull(),
                        TextEntry::make('currentVersion.developer_prompt')
                            ->label('Developer Prompt')
                            ->prose()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
