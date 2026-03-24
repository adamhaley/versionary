<?php

namespace Database\Seeders;

use App\Enums\AdapterCategory;
use App\Models\Adapter;
use Illuminate\Database\Seeder;

class AdapterSeeder extends Seeder
{
    /**
     * Common AI adapters to seed.
     *
     * @var array<int, array{
     *     name: string,
     *     slug: string,
     *     category: AdapterCategory,
     *     provider: string,
     *     config: array<string, string>,
     *     is_active: bool,
     *     supports_streaming: bool,
     *     supports_system_prompt: bool,
     *     supports_images: bool,
     *     supports_video: bool,
     *     supports_code: bool
     * }>
     */
    protected array $adapters = [
        // OpenAI Chat Models
        [
            'name' => 'GPT-4o',
            'slug' => 'openai-gpt-4o',
            'category' => AdapterCategory::Chat,
            'provider' => 'OpenAI',
            'config' => [
                'model' => 'gpt-4o',
                'api_key' => '',
                'max_tokens' => '4096',
            ],
            'is_active' => true,
            'supports_streaming' => true,
            'supports_system_prompt' => true,
            'supports_images' => true,
            'supports_video' => false,
            'supports_code' => true,
        ],
        [
            'name' => 'GPT-4o Mini',
            'slug' => 'openai-gpt-4o-mini',
            'category' => AdapterCategory::Chat,
            'provider' => 'OpenAI',
            'config' => [
                'model' => 'gpt-4o-mini',
                'api_key' => '',
                'max_tokens' => '4096',
            ],
            'is_active' => true,
            'supports_streaming' => true,
            'supports_system_prompt' => true,
            'supports_images' => true,
            'supports_video' => false,
            'supports_code' => true,
        ],
        [
            'name' => 'OpenAI o1',
            'slug' => 'openai-o1',
            'category' => AdapterCategory::Chat,
            'provider' => 'OpenAI',
            'config' => [
                'model' => 'o1',
                'api_key' => '',
                'max_completion_tokens' => '32768',
            ],
            'is_active' => true,
            'supports_streaming' => true,
            'supports_system_prompt' => false,
            'supports_images' => true,
            'supports_video' => false,
            'supports_code' => true,
        ],
        [
            'name' => 'OpenAI o3-mini',
            'slug' => 'openai-o3-mini',
            'category' => AdapterCategory::Chat,
            'provider' => 'OpenAI',
            'config' => [
                'model' => 'o3-mini',
                'api_key' => '',
                'max_completion_tokens' => '65536',
                'reasoning_effort' => 'medium',
            ],
            'is_active' => true,
            'supports_streaming' => true,
            'supports_system_prompt' => false,
            'supports_images' => false,
            'supports_video' => false,
            'supports_code' => true,
        ],

        // Anthropic Models
        [
            'name' => 'Claude Sonnet 4',
            'slug' => 'anthropic-claude-sonnet-4',
            'category' => AdapterCategory::Chat,
            'provider' => 'Anthropic',
            'config' => [
                'model' => 'claude-sonnet-4-20250514',
                'api_key' => '',
                'max_tokens' => '8192',
            ],
            'is_active' => true,
            'supports_streaming' => true,
            'supports_system_prompt' => true,
            'supports_images' => true,
            'supports_video' => false,
            'supports_code' => true,
        ],
        [
            'name' => 'Claude Opus 4',
            'slug' => 'anthropic-claude-opus-4',
            'category' => AdapterCategory::Chat,
            'provider' => 'Anthropic',
            'config' => [
                'model' => 'claude-opus-4-20250514',
                'api_key' => '',
                'max_tokens' => '8192',
            ],
            'is_active' => true,
            'supports_streaming' => true,
            'supports_system_prompt' => true,
            'supports_images' => true,
            'supports_video' => false,
            'supports_code' => true,
        ],

        // Google Models
        [
            'name' => 'Gemini 2.0 Flash',
            'slug' => 'google-gemini-2-flash',
            'category' => AdapterCategory::Chat,
            'provider' => 'Google',
            'config' => [
                'model' => 'gemini-2.0-flash',
                'api_key' => '',
                'max_output_tokens' => '8192',
            ],
            'is_active' => true,
            'supports_streaming' => true,
            'supports_system_prompt' => true,
            'supports_images' => true,
            'supports_video' => true,
            'supports_code' => true,
        ],
        [
            'name' => 'Gemini 2.5 Pro',
            'slug' => 'google-gemini-25-pro',
            'category' => AdapterCategory::Chat,
            'provider' => 'Google',
            'config' => [
                'model' => 'gemini-2.5-pro-preview-05-06',
                'api_key' => '',
                'max_output_tokens' => '8192',
            ],
            'is_active' => true,
            'supports_streaming' => true,
            'supports_system_prompt' => true,
            'supports_images' => true,
            'supports_video' => true,
            'supports_code' => true,
        ],

        // Image Generation
        [
            'name' => 'DALL-E 3',
            'slug' => 'openai-dalle-3',
            'category' => AdapterCategory::Image,
            'provider' => 'OpenAI',
            'config' => [
                'model' => 'dall-e-3',
                'api_key' => '',
                'size' => '1024x1024',
                'quality' => 'standard',
            ],
            'is_active' => true,
            'supports_streaming' => false,
            'supports_system_prompt' => false,
            'supports_images' => false,
            'supports_video' => false,
            'supports_code' => false,
        ],

        // Local / Ollama Models
        [
            'name' => 'Llama 3.3 (Ollama)',
            'slug' => 'ollama-llama-33',
            'category' => AdapterCategory::Chat,
            'provider' => 'Ollama',
            'config' => [
                'model' => 'llama3.3',
                'base_url' => 'http://localhost:11434',
            ],
            'is_active' => false,
            'supports_streaming' => true,
            'supports_system_prompt' => true,
            'supports_images' => false,
            'supports_video' => false,
            'supports_code' => true,
        ],
        [
            'name' => 'Mistral (Ollama)',
            'slug' => 'ollama-mistral',
            'category' => AdapterCategory::Chat,
            'provider' => 'Ollama',
            'config' => [
                'model' => 'mistral',
                'base_url' => 'http://localhost:11434',
            ],
            'is_active' => false,
            'supports_streaming' => true,
            'supports_system_prompt' => true,
            'supports_images' => false,
            'supports_video' => false,
            'supports_code' => true,
        ],

        // Coding-specific
        [
            'name' => 'GPT-4o (Coding)',
            'slug' => 'openai-gpt-4o-coding',
            'category' => AdapterCategory::Coding,
            'provider' => 'OpenAI',
            'config' => [
                'model' => 'gpt-4o',
                'api_key' => '',
                'max_tokens' => '4096',
            ],
            'is_active' => true,
            'supports_streaming' => true,
            'supports_system_prompt' => true,
            'supports_images' => true,
            'supports_video' => false,
            'supports_code' => true,
        ],
        [
            'name' => 'Claude Sonnet 4 (Coding)',
            'slug' => 'anthropic-claude-sonnet-4-coding',
            'category' => AdapterCategory::Coding,
            'provider' => 'Anthropic',
            'config' => [
                'model' => 'claude-sonnet-4-20250514',
                'api_key' => '',
                'max_tokens' => '8192',
            ],
            'is_active' => true,
            'supports_streaming' => true,
            'supports_system_prompt' => true,
            'supports_images' => true,
            'supports_video' => false,
            'supports_code' => true,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->adapters as $adapterData) {
            if (Adapter::where('slug', $adapterData['slug'])->exists()) {
                $this->command->info("Skipping existing adapter: {$adapterData['name']}");

                continue;
            }

            Adapter::create($adapterData);

            $this->command->info("Created adapter: {$adapterData['name']}");
        }
    }
}
