<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CrmTag extends Model
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_tags';

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
        'account_id' => 'string',
        'created_at' => 'datetime',
    ];

    /**
     * Get the account that owns the tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the CRM notes associated with this tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\CrmNote, $this>
     */
    public function crmNotes(): BelongsToMany
    {
        return $this->belongsToMany(CrmNote::class, 'crm_note_tags', 'tag_id', 'note_id');
    }
}
