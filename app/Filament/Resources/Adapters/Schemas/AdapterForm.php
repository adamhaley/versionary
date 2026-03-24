<?php

namespace App\Filament\Resources\Adapters\Schemas;

use App\Enums\AdapterCategory;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AdapterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
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
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        Select::make('category')
                            ->options(collect(AdapterCategory::cases())->mapWithKeys(fn (AdapterCategory $c) => [$c->value => $c->label()]))
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        TextInput::make('provider')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                    ]),

                Section::make('Capabilities')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Toggle::make('supports_streaming')
                            ->label('Streaming'),
                        Toggle::make('supports_system_prompt')
                            ->label('System Prompt'),
                        Toggle::make('supports_images')
                            ->label('Images'),
                        Toggle::make('supports_video')
                            ->label('Video'),
                        Toggle::make('supports_code')
                            ->label('Code'),
                    ]),

                Section::make('Configuration')
                    ->description('Provider-specific key/value settings (stored encrypted).')
                    ->schema([
                        KeyValue::make('config')
                            ->label('Config')
                            ->keyLabel('Setting')
                            ->valueLabel('Value')
                            ->reorderable(),
                    ]),
            ]);
    }
}
