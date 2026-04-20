<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'file_path',
        'error_message',
    ];

    /**
     * Get the order that owns the export.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
