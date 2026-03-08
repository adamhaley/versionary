<?php

namespace App\Models;

use App\Enums\AdapterCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptCategory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'prompt_id',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'category' => AdapterCategory::class,
        ];
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }
}
