<?php

namespace App\Filament\Resources\TestRuns\Pages;

use App\Filament\Resources\TestRuns\TestRunResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTestRun extends CreateRecord
{
    protected static string $resource = TestRunResource::class;
}
