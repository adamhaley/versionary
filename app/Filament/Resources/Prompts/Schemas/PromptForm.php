<?php

namespace App\Filament\Resources\Prompts\Schemas;

use App\Enums\AdapterCategory;
use App\Models\Prompt;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PromptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Prompt Identity')
                    ->description('Metadata that describes this prompt as a long-lived asset.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 400)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                            ->columnSpan(1),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(table: 'prompts', ignorable: fn ($record) => $record)
                            ->columnSpan(1),

                        Textarea::make('summary')
                            ->rows(2)
                            ->columnSpanFull(),

                        CheckboxList::make('categories')
                            ->label('Adapter Categories')
                            ->options(collect(AdapterCategory::cases())->mapWithKeys(fn (AdapterCategory $c) => [$c->value => $c->label()]))
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Version Content')
                    ->description('The content of this version. Each save creates an immutable new version.')
                    ->schema([
                        Select::make('_branch')
                            ->label('Branch')
                            ->visible(fn (string $operation) => $operation === 'edit')
                            ->options(fn ($record) => $record instanceof Prompt
                                ? $record->branches()->orderBy('name')->pluck('name', 'name')->toArray()
                                : ['main' => 'main'])
                            ->native(false)
                            ->required()
                            ->helperText('Which branch this version belongs to.'),

                        TextInput::make('title')
                            ->maxLength(255)
                            ->helperText('Optional short title for this version.'),

                        Textarea::make('system_prompt')
                            ->label('System Prompt')
                            ->rows(5),

                        Textarea::make('user_prompt_template')
                            ->label('User Prompt Template')
                            ->rows(6),

                        Textarea::make('developer_prompt')
                            ->label('Developer / Instruction Prompt')
                            ->rows(4),

                        TextInput::make('notes')
                            ->label('Version Notes')
                            ->maxLength(500)
                            ->helperText('Describe what changed in this version (like a commit message).'),
                    ]),
            ]);
    }
}
