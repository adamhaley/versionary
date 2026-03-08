<?php

namespace App\Contracts;

use App\Enums\TestRunStatus;

class AdapterExecutionResult
{
    public function __construct(
        public readonly TestRunStatus $status,
        public readonly ?string $normalizedOutput,
        public readonly array $renderedRequest,
        public readonly array $rawResponse,
        public readonly int $latencyMs,
        public readonly ?int $tokenUsageInput,
        public readonly ?int $tokenUsageOutput,
        public readonly ?float $estimatedCost,
        public readonly ?string $errorMessage,
    ) {}

    public static function success(
        string $normalizedOutput,
        array $renderedRequest,
        array $rawResponse,
        int $latencyMs,
        ?int $tokenUsageInput = null,
        ?int $tokenUsageOutput = null,
        ?float $estimatedCost = null,
    ): self {
        return new self(
            status: TestRunStatus::Completed,
            normalizedOutput: $normalizedOutput,
            renderedRequest: $renderedRequest,
            rawResponse: $rawResponse,
            latencyMs: $latencyMs,
            tokenUsageInput: $tokenUsageInput,
            tokenUsageOutput: $tokenUsageOutput,
            estimatedCost: $estimatedCost,
            errorMessage: null,
        );
    }

    public static function failure(
        string $errorMessage,
        array $renderedRequest = [],
        int $latencyMs = 0,
    ): self {
        return new self(
            status: TestRunStatus::Failed,
            normalizedOutput: null,
            renderedRequest: $renderedRequest,
            rawResponse: [],
            latencyMs: $latencyMs,
            tokenUsageInput: null,
            tokenUsageOutput: null,
            estimatedCost: null,
            errorMessage: $errorMessage,
        );
    }
}
