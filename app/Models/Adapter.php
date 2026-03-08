<?php

namespace App\Models;

use App\Enums\AdapterCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Adapter extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'provider',
        'driver_class',
        'config',
        'is_active',
        'supports_streaming',
        'supports_system_prompt',
        'supports_images',
        'supports_video',
        'supports_code',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'category' => AdapterCategory::class,
            'config' => 'array',
            'is_active' => 'boolean',
            'supports_streaming' => 'boolean',
            'supports_system_prompt' => 'boolean',
            'supports_images' => 'boolean',
            'supports_video' => 'boolean',
            'supports_code' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function testRuns(): HasMany
    {
        return $this->hasMany(TestRun::class);
    }
}
