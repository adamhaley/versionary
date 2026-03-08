<?php

namespace App\Models;

use App\Enums\AdapterCategory;
use App\Enums\PromptStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prompt extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'summary',
        'status',
        'current_version_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => PromptStatus::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class, 'current_version_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PromptVersion::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(PromptBranch::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(PromptCategory::class);
    }

    public function testRuns(): HasMany
    {
        return $this->hasMany(TestRun::class);
    }

    /** @return array<int, AdapterCategory> */
    public function adapterCategories(): array
    {
        return $this->categories->map(
            fn (PromptCategory $c) => AdapterCategory::from($c->category)
        )->all();
    }
}
