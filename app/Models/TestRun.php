<?php

namespace App\Models;

use App\Enums\TestRunStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestRun extends Model
{
    protected $fillable = [
        'prompt_id',
        'prompt_version_id',
        'adapter_id',
        'initiated_by',
        'rendered_request',
        'raw_response',
        'normalized_output',
        'status',
        'error_message',
        'latency_ms',
        'token_usage_input',
        'token_usage_output',
        'estimated_cost',
    ];

    protected function casts(): array
    {
        return [
            'status' => TestRunStatus::class,
            'rendered_request' => 'array',
            'raw_response' => 'array',
            'estimated_cost' => 'decimal:6',
        ];
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    public function promptVersion(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class);
    }

    public function adapter(): BelongsTo
    {
        return $this->belongsTo(Adapter::class);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
}
