<?php

namespace App\Domains\Crm\Services;

use App\Interfaces\ServiceInterface;
use App\Models\ContactFeedItem;
use App\Models\CrmNote;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UpdateCrmNote extends BaseService implements ServiceInterface
{
    private CrmNote $crmNote;

    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|uuid|exists:accounts,id',
            'vault_id' => 'required|uuid|exists:vaults,id',
            'author_id' => 'required|uuid|exists:users,id',
            'crm_note_id' => 'required|uuid|exists:crm_notes,id',
            'contact_id' => 'nullable|uuid|exists:contacts,id',
            'note_type_id' => 'nullable|uuid|exists:crm_note_types,id',
            'title' => 'nullable|string|max:255',
            'body' => 'required|string|max:65535',
            'noted_at' => 'nullable|date',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'vault_must_belong_to_account',
            'author_must_be_vault_editor',
        ];
    }

    /**
     * Update a CRM note.
     */
    public function execute(array $data): CrmNote
    {
        $this->validateRules($data);

        $this->crmNote = CrmNote::where('account_id', $data['account_id'])
            ->findOrFail($data['crm_note_id']);

        // If a contact is provided, validate it belongs to the vault
        if ($this->valueOrNull($data, 'contact_id')) {
            $this->vault->contacts()->findOrFail($data['contact_id']);
        }

        $this->crmNote->primary_contact_id = $this->valueOrNull($data, 'contact_id')
            ?? $this->crmNote->primary_contact_id;
        $this->crmNote->note_type_id = $this->valueOrNull($data, 'note_type_id');
        $this->crmNote->title = $this->valueOrNull($data, 'title');
        $this->crmNote->body = $data['body'];

        if ($this->valueOrNull($data, 'noted_at')) {
            $this->crmNote->noted_at = $data['noted_at'];
        }

        $this->crmNote->save();

        // Re-parse @mentions and #hashtags
        (new ParseNoteBody)->execute($this->crmNote, $data['account_id']);

        // Update contact last_interaction_at
        if ($this->crmNote->primary_contact_id) {
            $contact = $this->crmNote->primaryContact;
            $contact->last_interaction_at = Carbon::now();
            $contact->last_updated_at = Carbon::now();
            $contact->save();

            $this->createFeedItem($contact->id);
        }

        return $this->crmNote->load(['tags', 'noteContacts.contact', 'attachments']);
    }

    private function createFeedItem(string $contactId): void
    {
        ContactFeedItem::create([
            'author_id' => $this->author->id,
            'contact_id' => $contactId,
            'action' => ContactFeedItem::ACTION_NOTE_UPDATED,
            'description' => Str::words($this->crmNote->body, 10, '…'),
        ]);
    }
}
