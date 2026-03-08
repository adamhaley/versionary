<?php

namespace App\Filament\Widgets;

use App\Enums\TestRunStatus;
use App\Filament\Resources\TestRuns\TestRunResource;
use App\Models\TestRun;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTestRuns extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Test Runs')
            ->query(fn (): Builder => TestRun::query()->with(['prompt', 'adapter', 'promptVersion'])->latest()->limit(10))
            ->columns([
                TextColumn::make('prompt.name')
                    ->label('Prompt')
                    ->searchable(),

                TextColumn::make('promptVersion.branch_name')
                    ->label('Branch')
                    ->badge()
                    ->color('info'),

                TextColumn::make('promptVersion.version_number')
                    ->label('v')
                    ->prefix('v'),

                TextColumn::make('adapter.name')
                    ->label('Adapter'),

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

                TextColumn::make('created_at')
                    ->label('When')
                    ->since(),
            ])
            ->recordActions([
                Action::make('view')
                    ->url(fn (TestRun $record) => TestRunResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye')
                    ->color('gray'),
            ]);
    }
}
