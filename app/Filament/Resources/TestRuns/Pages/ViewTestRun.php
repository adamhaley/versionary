<?php

namespace App\Filament\Resources\TestRuns\Pages;

use App\Filament\Resources\TestRuns\TestRunResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTestRun extends ViewRecord
{
    protected static string $resource = TestRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
