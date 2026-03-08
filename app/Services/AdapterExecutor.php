<?php

namespace App\Services;

use App\Adapters\MockAdapterDriver;
use App\Contracts\AdapterDriverInterface;
use App\Contracts\AdapterExecutionResult;
use App\Enums\TestRunStatus;
use App\Models\Adapter;
use App\Models\PromptVersion;
use App\Models\TestRun;

class AdapterExecutor
{
    public function run(
        Adapter $adapter,
        PromptVersion $version,
        int $initiatedBy,
    ): TestRun {
        $testRun = TestRun::create([
            'prompt_id' => $version->prompt_id,
            'prompt_version_id' => $version->id,
            'adapter_id' => $adapter->id,
            'initiated_by' => $initiatedBy,
            'status' => TestRunStatus::Running,
        ]);

        try {
            $driver = $this->resolveDriver($adapter);
            $result = $driver->execute($adapter, $version);
            $this->applyResult($testRun, $result);
        } catch (\Throwable $e) {
            $this->applyResult($testRun, AdapterExecutionResult::failure(
                errorMessage: $e->getMessage(),
            ));
        }

        return $testRun->fresh();
    }

    private function resolveDriver(Adapter $adapter): AdapterDriverInterface
    {
        if ($adapter->driver_class && class_exists($adapter->driver_class)) {
            $driver = app($adapter->driver_class);

            if ($driver instanceof AdapterDriverInterface) {
                return $driver;
            }
        }

        return new MockAdapterDriver;
    }

    private function applyResult(TestRun $testRun, AdapterExecutionResult $result): void
    {
        $testRun->update([
            'status' => $result->status,
            'normalized_output' => $result->normalizedOutput,
            'rendered_request' => $result->renderedRequest,
            'raw_response' => $result->rawResponse,
            'latency_ms' => $result->latencyMs,
            'token_usage_input' => $result->tokenUsageInput,
            'token_usage_output' => $result->tokenUsageOutput,
            'estimated_cost' => $result->estimatedCost,
            'error_message' => $result->errorMessage,
        ]);
    }
}
