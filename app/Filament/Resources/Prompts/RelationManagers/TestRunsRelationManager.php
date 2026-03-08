<?php

namespace App\Filament\Resources\Prompts\RelationManagers;

use App\Enums\TestRunStatus;
use App\Filament\Resources\TestRuns\TestRunResource;
use App\Models\Adapter;
use App\Models\Prompt;
use App\Models\PromptVersion;
use App\Services\AdapterExecutor;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestRunsRelationManager extends RelationManager
{
    protected static string $relationship = 'testRuns';

    protected static ?string $title = 'Test Runs';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('promptVersion.branch_name')
                    ->label('Branch')
                    ->badge()
                    ->color('info'),

                TextColumn::make('promptVersion.version_number')
                    ->label('Version')
                    ->prefix('v'),

                TextColumn::make('adapter.name')
                    ->label('Adapter'),

                TextColumn::make('adapter.category')
                    ->label('Category')
                    ->badge(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (TestRunStatus $state): string => match ($state) {
                        TestRunStatus::Pending => 'gray',
                        TestRunStatus::Running => 'info',
                        TestRunStatus::Completed => 'success',
                        TestRunStatus::Failed => 'danger',
                    }),

                TextColumn::make('latency_ms')
                    ->label('Latency')
                    ->suffix(' ms'),

                TextColumn::make('token_usage_output')
                    ->label('Output Tokens'),

                TextColumn::make('created_at')
                    ->label('Run At')
                    ->since()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('run_test')
                    ->label('Run Test')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->modalHeading('Run Prompt Test')
                    ->schema(function (): array {
                        /** @var Prompt $prompt */
                        $prompt = $this->ownerRecord;

                        return [
                            Select::make('prompt_version_id')
                                ->label('Version')
                                ->options(
                                    $prompt->versions()
                                        ->orderBy('created_at', 'desc')
                                        ->get()
                                        ->mapWithKeys(fn (PromptVersion $v) => [
                                            $v->id => "[{$v->branch_name}] v{$v->version_number}".($v->title ? " — {$v->title}" : ''),
                                        ])
                                )
                                ->default($prompt->current_version_id)
                                ->required()
                                ->native(false),

                            Select::make('adapter_id')
                                ->label('Adapter')
                                ->options(
                                    Adapter::query()
                                        ->where('is_active', true)
                                        ->get()
                                        ->mapWithKeys(fn (Adapter $a) => [
                                            $a->id => "[{$a->category->value}] {$a->name}",
                                        ])
                                )
                                ->required()
                                ->native(false),
                        ];
                    })
                    ->action(function (array $data): void {
                        $version = PromptVersion::findOrFail($data['prompt_version_id']);
                        $adapter = Adapter::findOrFail($data['adapter_id']);

                        $testRun = app(AdapterExecutor::class)->run(
                            adapter: $adapter,
                            version: $version,
                            initiatedBy: auth()->id(),
                        );

                        $status = $testRun->status === TestRunStatus::Completed ? 'success' : 'danger';
                        $title = $testRun->status === TestRunStatus::Completed ? 'Test run completed' : 'Test run failed';

                        Notification::make()
                            ->title($title)
                            ->body("Latency: {$testRun->latency_ms}ms | Tokens: {$testRun->token_usage_output}")
                            ->{$status}()
                            ->send();
                    }),

                Action::make('compare_runs')
                    ->label('Compare Runs')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('gray')
                    ->modalHeading('Compare Two Test Runs')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('7xl')
                    ->schema(function (): array {
                        /** @var Prompt $prompt */
                        $prompt = $this->ownerRecord;

                        $runOptions = $prompt->testRuns()
                            ->with(['adapter', 'promptVersion'])
                            ->latest()
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($r) => [
                                $r->id => "[{$r->promptVersion?->branch_name}] v{$r->promptVersion?->version_number} — {$r->adapter?->name} ({$r->status->value})",
                            ])
                            ->toArray();

                        return [
                            Select::make('run_a_id')
                                ->label('Run A')
                                ->options($runOptions)
                                ->required()
                                ->native(false)
                                ->live(),

                            Select::make('run_b_id')
                                ->label('Run B')
                                ->options($runOptions)
                                ->required()
                                ->native(false)
                                ->live(),
                        ];
                    })
                    ->fillForm(fn () => []),
            ])
            ->recordActions([
                Action::make('view_run')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn ($record) => TestRunResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
