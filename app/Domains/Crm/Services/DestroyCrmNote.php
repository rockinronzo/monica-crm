<?php

namespace App\Domains\Crm\Services;

use App\Interfaces\ServiceInterface;
use App\Models\ContactFeedItem;
use App\Models\CrmNote;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DestroyCrmNote extends BaseService implements ServiceInterface
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
     * Destroy a CRM note.
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->crmNote = CrmNote::where('account_id', $data['account_id'])
            ->findOrFail($data['crm_note_id']);

        // Create feed item before deletion if there's a primary contact
        if ($this->crmNote->primary_contact_id) {
            $this->createFeedItem($this->crmNote->primary_contact_id);

            $contact = $this->crmNote->primaryContact;
            $contact->last_updated_at = Carbon::now();
            $contact->save();
        }

        $this->crmNote->delete();
    }

    private function createFeedItem(string $contactId): void
    {
        ContactFeedItem::create([
            'author_id' => $this->author->id,
            'contact_id' => $contactId,
            'action' => ContactFeedItem::ACTION_NOTE_DESTROYED,
            'description' => Str::words($this->crmNote->body, 10, '…'),
        ]);
    }
}
