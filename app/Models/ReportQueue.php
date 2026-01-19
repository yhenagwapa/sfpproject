<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportQueue extends Model
{
    use HasFactory;

    protected $table = 'report_queue';

    protected $fillable = [
        'user_id',
        'report',
        'status',
        'cdc_id',
        'file_path',
        'generated_at',
        'downloaded_at',
        'error_message',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    /**
     * Get the user that owns the report
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
