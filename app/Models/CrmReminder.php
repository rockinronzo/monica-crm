<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmReminder extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_reminders';

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
        'account_id',
        'contact_id',
        'note_id',
        'note_text',
        'remind_on',
        'completed',
        'created_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'account_id' => 'string',
        'contact_id' => 'string',
        'note_id' => 'string',
        'remind_on' => 'date',
        'completed' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the account that owns the reminder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the contact associated with the reminder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Contact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the CRM note associated with the reminder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\CrmNote, $this>
     */
    public function crmNote(): BelongsTo
    {
        return $this->belongsTo(CrmNote::class, 'note_id');
    }
}
