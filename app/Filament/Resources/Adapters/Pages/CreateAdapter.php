<?php

namespace App\Filament\Resources\Adapters\Pages;

use App\Filament\Resources\Adapters\AdapterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdapter extends CreateRecord
{
    protected static string $resource = AdapterResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
