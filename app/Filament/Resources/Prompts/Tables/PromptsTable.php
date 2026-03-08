<?php

namespace App\Filament\Resources\Prompts\Tables;

use App\Enums\AdapterCategory;
use App\Enums\PromptStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PromptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (PromptStatus $state): string => match ($state) {
                        PromptStatus::Draft => 'gray',
                        PromptStatus::Active => 'success',
                        PromptStatus::Archived => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('currentVersion.branch_name')
                    ->label('Branch')
                    ->badge()
                    ->color('info'),

                TextColumn::make('currentVersion.version_number')
                    ->label('Version')
                    ->prefix('v')
                    ->sortable(),

                TextColumn::make('updater.name')
                    ->label('Last Edited By')
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(PromptStatus::cases())->mapWithKeys(fn (PromptStatus $s) => [$s->value => $s->label()])),

                SelectFilter::make('categories')
                    ->label('Category')
                    ->relationship('categories', 'category')
                    ->options(collect(AdapterCategory::cases())->mapWithKeys(fn (AdapterCategory $c) => [$c->value => $c->label()])),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->label('New Version'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
