<?php

namespace App\Adapters;

use App\Contracts\AdapterDriverInterface;
use App\Contracts\AdapterExecutionResult;
use App\Enums\AdapterCategory;
use App\Models\Adapter;
use App\Models\PromptVersion;

class MockAdapterDriver implements AdapterDriverInterface
{
    public function execute(Adapter $adapter, PromptVersion $version): AdapterExecutionResult
    {
        $start = microtime(true);

        $renderedRequest = $this->buildRequest($adapter, $version);

        // Simulate a short delay
        usleep(random_int(200_000, 800_000));

        $latencyMs = (int) ((microtime(true) - $start) * 1000);

        $output = $this->generateMockOutput($adapter, $version);
        $inputTokens = str_word_count(implode(' ', array_filter([
            $version->system_prompt,
            $version->user_prompt_template,
            $version->developer_prompt,
        ])));
        $outputTokens = str_word_count($output);

        return AdapterExecutionResult::success(
            normalizedOutput: $output,
            renderedRequest: $renderedRequest,
            rawResponse: [
                'id' => 'mock-'.uniqid(),
                'model' => $adapter->provider.'/mock-model',
                'object' => 'chat.completion',
                'choices' => [
                    [
                        'index' => 0,
                        'message' => ['role' => 'assistant', 'content' => $output],
                        'finish_reason' => 'stop',
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => $inputTokens,
                    'completion_tokens' => $outputTokens,
                    'total_tokens' => $inputTokens + $outputTokens,
                ],
            ],
            latencyMs: $latencyMs,
            tokenUsageInput: $inputTokens,
            tokenUsageOutput: $outputTokens,
            estimatedCost: round(($inputTokens * 0.000003) + ($outputTokens * 0.000015), 6),
        );
    }

    public function validateConfig(Adapter $adapter): bool
    {
        return true;
    }

    /** @return array<string> */
    public function supportedCapabilities(): array
    {
        return ['system_prompt', 'user_prompt', 'streaming'];
    }

    private function buildRequest(Adapter $adapter, PromptVersion $version): array
    {
        $messages = [];

        if ($version->system_prompt && $adapter->supports_system_prompt) {
            $messages[] = ['role' => 'system', 'content' => $version->system_prompt];
        }

        if ($version->developer_prompt) {
            $messages[] = ['role' => 'system', 'content' => $version->developer_prompt];
        }

        if ($version->user_prompt_template) {
            $messages[] = ['role' => 'user', 'content' => $version->user_prompt_template];
        }

        return [
            'model' => $adapter->provider.'/mock-model',
            'messages' => $messages,
            'max_tokens' => 1024,
            'temperature' => 0.7,
        ];
    }

    private function generateMockOutput(Adapter $adapter, PromptVersion $version): string
    {
        $category = $adapter->category instanceof AdapterCategory ? $adapter->category : AdapterCategory::from($adapter->category);

        return match ($category) {
            AdapterCategory::Chat => $this->mockChatResponse($version),
            AdapterCategory::Coding => $this->mockCodingResponse($version),
            AdapterCategory::Image => '[Mock image generated: prompt submitted to '.$adapter->provider.']',
            AdapterCategory::Video => '[Mock video generated: prompt submitted to '.$adapter->provider.']',
        };
    }

    private function mockChatResponse(PromptVersion $version): string
    {
        $title = $version->title ?? 'this prompt';

        return "This is a mock chat response for \"{$title}\". In a real execution, this would be the AI model's reply to your prompt. The system prompt, user prompt template, and developer instructions would all be processed by the provider's API and a real response returned here.";
    }

    private function mockCodingResponse(PromptVersion $version): string
    {
        return "```php\n// Mock coding response for: ".($version->title ?? 'prompt')."\n// In production, the AI model would generate real code here.\nfunction mockGeneratedFunction(): string\n{\n    return 'Hello from mock!';\n}\n```";
    }
}
