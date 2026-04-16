<?php

namespace App\Models;

use App\Models\Enums\ContactFactSource;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmContactFact extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_contact_facts';

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
        'contact_id',
        'note_id',
        'label',
        'value',
        'source',
        'confirmed',
        'created_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'contact_id' => 'string',
        'note_id' => 'string',
        'source' => ContactFactSource::class,
        'confirmed' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the contact that owns the fact.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Contact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the CRM note this fact was extracted from.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CrmNote, $this>
     */
    public function crmNote(): BelongsTo
    {
        return $this->belongsTo(CrmNote::class, 'note_id');
    }
}
