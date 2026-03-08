<?php

namespace App\Filament\Resources\TestRuns\Tables;

use App\Enums\TestRunStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TestRunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('prompt.name')
                    ->label('Prompt')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('promptVersion.branch_name')
                    ->label('Branch')
                    ->badge()
                    ->color('info'),

                TextColumn::make('promptVersion.version_number')
                    ->label('Version')
                    ->prefix('v'),

                TextColumn::make('adapter.name')
                    ->label('Adapter')
                    ->searchable(),

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
                    })
                    ->sortable(),

                TextColumn::make('latency_ms')
                    ->label('Latency')
                    ->suffix(' ms')
                    ->sortable(),

                TextColumn::make('initiator.name')
                    ->label('Run By')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Run At')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(TestRunStatus::cases())->mapWithKeys(fn (TestRunStatus $s) => [$s->value => $s->label()])),

                SelectFilter::make('adapter')
                    ->relationship('adapter', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
