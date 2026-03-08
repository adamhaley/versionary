<?php

namespace App\Filament\Widgets;

use App\Enums\PromptStatus;
use App\Enums\TestRunStatus;
use App\Models\Prompt;
use App\Models\TestRun;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PromptsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $activePrompts = Prompt::query()->where('status', PromptStatus::Active)->count();
        $totalPrompts = Prompt::query()->count();
        $totalVersions = \App\Models\PromptVersion::query()->count();
        $totalBranches = \App\Models\PromptBranch::query()->count();
        $runsThisWeek = TestRun::query()->where('created_at', '>=', now()->subWeek())->count();
        $successRate = TestRun::query()->count() > 0
            ? round((TestRun::query()->where('status', TestRunStatus::Completed)->count() / TestRun::query()->count()) * 100)
            : 0;

        return [
            Stat::make('Active Prompts', $activePrompts)
                ->description("{$totalPrompts} total")
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success'),

            Stat::make('Versions Created', $totalVersions)
                ->description("{$totalBranches} branches")
                ->descriptionIcon('heroicon-o-code-bracket')
                ->color('info'),

            Stat::make('Test Runs This Week', $runsThisWeek)
                ->description("{$successRate}% success rate")
                ->descriptionIcon('heroicon-o-beaker')
                ->color($successRate >= 80 ? 'success' : ($successRate >= 50 ? 'warning' : 'danger')),
        ];
    }
}
