<?php

namespace App\Filament\Resources\TestRuns\Pages;

use App\Filament\Resources\Prompts\PromptResource;
use App\Filament\Resources\TestRuns\TestRunResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTestRun extends ViewRecord
{
    protected static string $resource = TestRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_prompt')
                ->label('View Prompt')
                ->color('gray')
                ->url(fn () => PromptResource::getUrl('view', ['record' => $this->record->prompt_id])),
        ];
    }
}
