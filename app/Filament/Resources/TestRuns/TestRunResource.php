<?php

namespace App\Filament\Resources\TestRuns;

use App\Filament\Resources\TestRuns\Pages\ListTestRuns;
use App\Filament\Resources\TestRuns\Pages\ViewTestRun;
use App\Filament\Resources\TestRuns\Schemas\TestRunInfolist;
use App\Filament\Resources\TestRuns\Tables\TestRunsTable;
use App\Models\TestRun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TestRunResource extends Resource
{
    protected static ?string $model = TestRun::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $navigationLabel = 'Test Runs';

    protected static ?int $navigationSort = 3;

    public static function infolist(Schema $schema): Schema
    {
        return TestRunInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TestRunsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestRuns::route('/'),
            'view' => ViewTestRun::route('/{record}'),
        ];
    }
}
