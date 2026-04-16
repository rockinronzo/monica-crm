<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CrmNoteTag extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_note_tags';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key type.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'note_id',
        'tag_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'note_id' => 'string',
        'tag_id' => 'string',
    ];

    /**
     * Get the CRM note associated with this pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CrmNote, $this>
     */
    public function crmNote(): BelongsTo
    {
        return $this->belongsTo(CrmNote::class, 'note_id');
    }

    /**
     * Get the CRM tag associated with this pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CrmTag, $this>
     */
    public function crmTag(): BelongsTo
    {
        return $this->belongsTo(CrmTag::class, 'tag_id');
    }
}
