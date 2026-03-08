<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptBranch extends Model
{
    protected $fillable = [
        'prompt_id',
        'name',
        'base_version_id',
        'created_by',
    ];

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    public function baseVersion(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class, 'base_version_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
