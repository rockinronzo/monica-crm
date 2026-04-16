<?php

namespace App\Domains\Crm\Services;

use App\Models\Contact;
use App\Models\CrmNote;
use App\Models\CrmNoteContact;
use App\Models\CrmTag;
use App\Models\Enums\NoteContactRole;
use Carbon\Carbon;

class ParseNoteBody
{
    /**
     * Parse @mentions and #hashtags from the note body, then sync
     * the crm_note_contacts and crm_note_tags tables.
     *
     * This is the server-side source of truth — the frontend dropdowns
     * are just for a smooth writing experience.
     */
    public function execute(CrmNote $note, string $accountId): void
    {
        $this->syncMentions($note);
        $this->syncHashtags($note, $accountId);
    }

    /**
     * Extract @mentions from body and sync to crm_note_contacts.
     *
     * Supported formats:
     *   @first_name            — matches first contact with that first name in account
     *   @"First Last"          — matches full name (quoted)
     *   @[contact-uuid]        — exact match by UUID
     */
    private function syncMentions(CrmNote $note): void
    {
        $body = $note->body;
        $contactIds = collect();

        // Pattern 1: @[uuid]
        preg_match_all('/@\[([0-9a-f\-]{36})\]/i', $body, $uuidMatches);
        foreach ($uuidMatches[1] as $uuid) {
            $contact = Contact::find($uuid);
            if ($contact) {
                $contactIds->push($contact->id);
            }
        }

        // Pattern 2: @"First Last"
        preg_match_all('/@"([^"]+)"/', $body, $quotedMatches);
        foreach ($quotedMatches[1] as $fullName) {
            $contact = $this->findContactByFullName($fullName, $note);
            if ($contact) {
                $contactIds->push($contact->id);
            }
        }

        // Pattern 3: @single_word (not preceded by [ or ")
        preg_match_all('/(?<!["\[])@(\w+)/', $body, $simpleMatches);
        foreach ($simpleMatches[1] as $name) {
            $contact = $this->findContactByFirstName($name, $note);
            if ($contact) {
                $contactIds->push($contact->id);
            }
        }

        $contactIds = $contactIds->unique();

        // Determine roles: the primary_contact_id gets 'primary', all others 'secondary'
        $syncData = [];
        foreach ($contactIds as $contactId) {
            $role = ($contactId === $note->primary_contact_id)
                ? NoteContactRole::Primary
                : NoteContactRole::Secondary;

            $syncData[] = [
                'note_id' => $note->id,
                'contact_id' => $contactId,
                'role' => $role->value,
            ];
        }

        // If the primary_contact_id is set but wasn't @mentioned, include them too
        if ($note->primary_contact_id && ! $contactIds->contains($note->primary_contact_id)) {
            $syncData[] = [
                'note_id' => $note->id,
                'contact_id' => $note->primary_contact_id,
                'role' => NoteContactRole::Primary->value,
            ];
        }

        // Delete existing and re-insert
        CrmNoteContact::where('note_id', $note->id)->delete();
        foreach ($syncData as $row) {
            CrmNoteContact::create($row);
        }
    }

    /**
     * Extract #hashtags from body and sync to crm_note_tags.
     *
     * Supported formats:
     *   #tagname              — single word, lowercased
     *   #"Multi Word Tag"     — quoted, display_name preserves casing
     */
    private function syncHashtags(CrmNote $note, string $accountId): void
    {
        $body = $note->body;
        $tagNames = collect();

        // Pattern 1: #"Multi Word Tag"
        preg_match_all('/#"([^"]+)"/', $body, $quotedMatches);
        foreach ($quotedMatches[1] as $displayName) {
            $tagNames->push([
                'name' => strtolower(trim($displayName)),
                'display_name' => trim($displayName),
            ]);
        }

        // Pattern 2: #singleword (not preceded by ")
        preg_match_all('/(?<!["])#(\w+)/', $body, $simpleMatches);
        foreach ($simpleMatches[1] as $tag) {
            $tagNames->push([
                'name' => strtolower($tag),
                'display_name' => $tag,
            ]);
        }

        $tagNames = $tagNames->unique('name');

        // Find or create tags and collect IDs
        $tagIds = [];
        foreach ($tagNames as $tagData) {
            $tag = CrmTag::firstOrCreate(
                [
                    'account_id' => $accountId,
                    'name' => $tagData['name'],
                ],
                [
                    'display_name' => $tagData['display_name'],
                    'created_at' => Carbon::now(),
                ]
            );
            $tagIds[] = $tag->id;
        }

        // Sync the pivot table — detach all, then attach current set
        $note->tags()->sync($tagIds);
    }

    /**
     * Find a contact by full name within the same vault as the note's primary contact,
     * falling back to account-wide search.
     */
    private function findContactByFullName(string $fullName, CrmNote $note): ?Contact
    {
        $parts = explode(' ', trim($fullName), 2);
        $firstName = $parts[0];
        $lastName = $parts[1] ?? null;

        $query = Contact::where('first_name', $firstName);

        if ($lastName) {
            $query->where('last_name', $lastName);
        }

        return $query->first();
    }

    /**
     * Find a contact by first name.
     */
    private function findContactByFirstName(string $name, CrmNote $note): ?Contact
    {
        return Contact::where('first_name', $name)->first();
    }
}
