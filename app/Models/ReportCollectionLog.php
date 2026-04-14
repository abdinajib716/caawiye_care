<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCollectionLog extends Model
{
    protected $fillable = [
        'report_collection_id',
        'action',
        'old_value',
        'new_value',
        'notes',
        'performed_by',
    ];

    public function reportCollection(): BelongsTo
    {
        return $this->belongsTo(ReportCollection::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
