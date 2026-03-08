<?php

namespace App\Contracts;

use App\Models\Adapter;
use App\Models\PromptVersion;

interface AdapterDriverInterface
{
    public function execute(Adapter $adapter, PromptVersion $version): AdapterExecutionResult;

    public function validateConfig(Adapter $adapter): bool;

    /** @return array<string> */
    public function supportedCapabilities(): array;
}
