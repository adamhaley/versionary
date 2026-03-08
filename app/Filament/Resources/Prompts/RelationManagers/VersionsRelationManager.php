<?php

namespace App\Filament\Resources\Prompts\RelationManagers;

use App\EventSourcing\Aggregates\PromptAggregate;
use App\Models\Prompt;
use App\Models\PromptVersion;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VersionsRelationManager extends RelationManager
{
    protected static string $relationship = 'versions';

    protected static ?string $title = 'Version History';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('version_number')
                    ->label('Version')
                    ->prefix('v')
                    ->sortable(),

                TextColumn::make('branch_name')
                    ->label('Branch')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(60)
                    ->color('gray'),

                IconColumn::make('is_restored')
                    ->label('Restored')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-uturn-left')
                    ->falseIcon('')
                    ->trueColor('warning'),

                TextColumn::make('creator.name')
                    ->label('By'),

                TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                Action::make('view_content')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->schema(fn (PromptVersion $record): array => [
                        Section::make('Version Details')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('branch_name')->label('Branch')->badge()->color('info'),
                                TextEntry::make('version_number')->label('Version')->prefix('v'),
                                TextEntry::make('created_at')->label('Created')->dateTime(),
                                TextEntry::make('title')->label('Title'),
                                TextEntry::make('notes')->label('Notes'),
                                TextEntry::make('creator.name')->label('By'),
                            ]),
                        Section::make('Content')
                            ->schema([
                                TextEntry::make('system_prompt')->label('System Prompt')->prose()->columnSpanFull(),
                                TextEntry::make('user_prompt_template')->label('User Prompt Template')->prose()->columnSpanFull(),
                                TextEntry::make('developer_prompt')->label('Developer Prompt')->prose()->columnSpanFull(),
                            ]),
                    ])
                    ->fillForm(fn (PromptVersion $record): array => $record->toArray()),

                Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (PromptVersion $record) => "Restore v{$record->version_number} ({$record->branch_name})")
                    ->modalDescription('This creates a new version on the same branch with the content from this version. History is preserved.')
                    ->schema([
                        TextInput::make('notes')
                            ->label('Version Notes')
                            ->placeholder('Restored from version ...')
                            ->maxLength(500),
                    ])
                    ->action(function (PromptVersion $record, array $data): void {
                        /** @var Prompt $prompt */
                        $prompt = $this->ownerRecord;

                        PromptAggregate::retrieve($prompt->id)
                            ->restoreVersion(
                                fromVersionId: $record->id,
                                branchName: $record->branch_name,
                                parentVersionId: $prompt->current_version_id,
                                restoredBy: auth()->id(),
                                title: $record->title,
                                systemPrompt: $record->system_prompt,
                                userPromptTemplate: $record->user_prompt_template,
                                developerPrompt: $record->developer_prompt,
                            )
                            ->persist();

                        Notification::make()
                            ->title("Restored to v{$record->version_number}")
                            ->success()
                            ->send();
                    }),

                Action::make('branch_from_here')
                    ->label('Branch')
                    ->icon('heroicon-o-code-bracket')
                    ->color('info')
                    ->modalHeading(fn (PromptVersion $record) => "Branch from v{$record->version_number}")
                    ->modalDescription('Creates a new named branch starting from this version.')
                    ->schema([
                        TextInput::make('branch_name')
                            ->label('Branch Name')
                            ->required()
                            ->maxLength(100)
                            ->helperText('e.g. concise-tone-experiment, gpt-4o-pass')
                            ->rules(['alpha_dash']),
                    ])
                    ->action(function (PromptVersion $record, array $data): void {
                        /** @var Prompt $prompt */
                        $prompt = $this->ownerRecord;

                        PromptAggregate::retrieve($prompt->id)
                            ->createBranch(
                                branchName: $data['branch_name'],
                                baseVersionId: $record->id,
                                createdBy: auth()->id(),
                            )
                            ->persist();

                        Notification::make()
                            ->title("Branch '{$data['branch_name']}' created")
                            ->body('Add a new version on this branch from the edit page.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
