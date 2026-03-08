<?php

namespace App\Filament\Resources\Adapters\Pages;

use App\Filament\Resources\Adapters\AdapterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdapters extends ListRecords
{
    protected static string $resource = AdapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
