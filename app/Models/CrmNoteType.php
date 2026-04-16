<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmNoteType extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_note_types';

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
        'name',
        'display_name',
        'created_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the CRM notes associated with this note type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CrmNote, $this>
     */
    public function crmNotes(): HasMany
    {
        return $this->hasMany(CrmNote::class, 'note_type_id');
    }
}
