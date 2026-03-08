<?php

namespace App\Filament\Resources\TestRuns\Schemas;

use App\Enums\TestRunStatus;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TestRunInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Run Summary')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (TestRunStatus $state): string => match ($state) {
                                TestRunStatus::Pending => 'gray',
                                TestRunStatus::Running => 'info',
                                TestRunStatus::Completed => 'success',
                                TestRunStatus::Failed => 'danger',
                            }),

                        TextEntry::make('adapter.name')->label('Adapter'),

                        TextEntry::make('latency_ms')
                            ->label('Latency')
                            ->suffix(' ms'),

                        TextEntry::make('prompt.name')->label('Prompt'),

                        TextEntry::make('promptVersion.branch_name')
                            ->label('Branch')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('promptVersion.version_number')
                            ->label('Version')
                            ->prefix('v'),

                        TextEntry::make('token_usage_input')
                            ->label('Input Tokens'),

                        TextEntry::make('token_usage_output')
                            ->label('Output Tokens'),

                        TextEntry::make('estimated_cost')
                            ->label('Est. Cost')
                            ->prefix('$'),

                        TextEntry::make('initiator.name')
                            ->label('Run By'),

                        TextEntry::make('created_at')
                            ->label('Run At')
                            ->dateTime(),
                    ]),

                Section::make('Output')
                    ->schema([
                        TextEntry::make('normalized_output')
                            ->label('Normalized Output')
                            ->prose()
                            ->columnSpanFull(),

                        TextEntry::make('error_message')
                            ->label('Error')
                            ->color('danger')
                            ->visible(fn ($record) => $record->status === TestRunStatus::Failed)
                            ->columnSpanFull(),
                    ]),

                Section::make('Request')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('rendered_request')
                            ->label('Rendered Request')
                            ->state(fn ($record) => json_encode($record->rendered_request, JSON_PRETTY_PRINT))
                            ->prose()
                            ->columnSpanFull(),
                    ]),

                Section::make('Raw Response')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('raw_response')
                            ->label('Raw Response')
                            ->state(fn ($record) => json_encode($record->raw_response, JSON_PRETTY_PRINT))
                            ->prose()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
