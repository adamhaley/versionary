<?php

namespace App\Filament\Resources\Adapters\Pages;

use App\Filament\Resources\Adapters\AdapterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdapter extends EditRecord
{
    protected static string $resource = AdapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
