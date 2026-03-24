<?php

namespace App\Filament\Resources\TestRuns\Pages;

use App\Filament\Resources\TestRuns\TestRunResource;
use Filament\Resources\Pages\ListRecords;

class ListTestRuns extends ListRecords
{
    protected static string $resource = TestRunResource::class;
}
