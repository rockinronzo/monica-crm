<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmNote extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'primary_contact_id',
        'note_type_id',
        'title',
        'body',
        'noted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'account_id' => 'string',
        'primary_contact_id' => 'string',
        'note_type_id' => 'string',
        'noted_at' => 'date',
    ];

    /**
     * Scope a query to a specific account.
     */
    public function scopeForAccount(Builder $query, string $accountId): Builder
    {
        return $query->where('account_id', $accountId);
    }

    /**
     * Get the account that owns the note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the primary contact associated with the note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Contact, $this>
     */
    public function primaryContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'primary_contact_id');
    }

    /**
     * Get the note type associated with the note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CrmNoteType, $this>
     */
    public function noteType(): BelongsTo
    {
        return $this->belongsTo(CrmNoteType::class, 'note_type_id');
    }

    /**
     * Get the note-contact junction records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CrmNoteContact, $this>
     */
    public function noteContacts(): HasMany
    {
        return $this->hasMany(CrmNoteContact::class, 'note_id');
    }

    /**
     * Get the tags associated with the note through the pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\CrmTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(CrmTag::class, 'crm_note_tags', 'note_id', 'tag_id');
    }

    /**
     * Get the attachments associated with the note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CrmAttachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(CrmAttachment::class, 'note_id');
    }

    /**
     * Get the reminders associated with the note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CrmReminder, $this>
     */
    public function crmReminders(): HasMany
    {
        return $this->hasMany(CrmReminder::class, 'note_id');
    }

    /**
     * Get the contact facts extracted from this note.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\CrmContactFact, $this>
     */
    public function contactFacts(): HasMany
    {
        return $this->hasMany(CrmContactFact::class, 'note_id');
    }
}
