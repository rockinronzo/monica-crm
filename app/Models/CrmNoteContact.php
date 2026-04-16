<?php

namespace App\Models;

use App\Models\Enums\NoteContactRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmNoteContact extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_note_contacts';

    /**
     * Indicates if the model should be timestamped.
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
        'contact_id',
        'role',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'note_id' => 'string',
        'contact_id' => 'string',
        'role' => NoteContactRole::class,
    ];

    /**
     * Get the CRM note associated with this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CrmNote, $this>
     */
    public function crmNote(): BelongsTo
    {
        return $this->belongsTo(CrmNote::class, 'note_id');
    }

    /**
     * Get the contact associated with this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Contact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
