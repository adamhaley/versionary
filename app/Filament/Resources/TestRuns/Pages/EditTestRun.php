<?php

namespace App\Filament\Resources\TestRuns\Pages;

use App\Filament\Resources\TestRuns\TestRunResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTestRun extends EditRecord
{
    protected static string $resource = TestRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
