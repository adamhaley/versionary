<?php

namespace App\Filament\Resources\Adapters;

use App\Filament\Resources\Adapters\Pages\CreateAdapter;
use App\Filament\Resources\Adapters\Pages\EditAdapter;
use App\Filament\Resources\Adapters\Pages\ListAdapters;
use App\Filament\Resources\Adapters\Schemas\AdapterForm;
use App\Filament\Resources\Adapters\Tables\AdaptersTable;
use App\Models\Adapter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdapterResource extends Resource
{
    protected static ?string $model = Adapter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?string $navigationLabel = 'Adapters';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AdapterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdaptersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdapters::route('/'),
            'create' => CreateAdapter::route('/create'),
            'edit' => EditAdapter::route('/{record}/edit'),
        ];
    }
}
