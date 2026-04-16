<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmAttachment extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_attachments';

    /**
     * Indicates if the model should be timestamped.
     * This table only has created_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'note_id',
        'file_path',
        'file_type',
        'original_name',
        'created_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'note_id' => 'string',
        'created_at' => 'datetime',
    ];

    /**
     * Get the CRM note that owns this attachment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CrmNote, $this>
     */
    public function crmNote(): BelongsTo
    {
        return $this->belongsTo(CrmNote::class, 'note_id');
    }
}
