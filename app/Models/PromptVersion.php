<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromptVersion extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'prompt_id',
        'version_number',
        'branch_name',
        'parent_version_id',
        'title',
        'system_prompt',
        'user_prompt_template',
        'developer_prompt',
        'notes',
        'is_restored',
        'restored_from_version_id',
        'created_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_restored' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class, 'parent_version_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(PromptVersion::class, 'parent_version_id');
    }

    public function restoredFrom(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class, 'restored_from_version_id');
    }

    public function testRuns(): HasMany
    {
        return $this->hasMany(TestRun::class);
    }
}
