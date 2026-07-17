<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RankingAlert extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'time_block_id',
        'affected_talks',
        'alert_type',
        'details',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'affected_talks' => 'array',
    ];

    /**
     * Get the time block that owns the alert.
     */
    public function timeBlock(): BelongsTo
    {
        return $this->belongsTo(TimeBlock::class);
    }
}
