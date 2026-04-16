<?php

namespace App\Domains\Crm\Web\ViewHelpers;

use App\Helpers\DateHelper;
use App\Models\CrmNote;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CrmNoteViewHelper
{
    /**
     * Data for the CRM notes index page.
     */
    public static function index(LengthAwarePaginator $notes, User $user, string $vaultId): array
    {
        $notesCollection = $notes->getCollection()->map(
            fn (CrmNote $note) => self::dto($note, $user, $vaultId)
        );

        return [
            'notes' => $notesCollection,
            'url' => [
                'store' => route('crm.notes.store', ['vault' => $vaultId]),
            ],
        ];
    }

    /**
     * Format a single CRM note for the frontend.
     */
    public static function dto(CrmNote $note, User $user, string $vaultId): array
    {
        $note->loadMissing(['primaryContact', 'noteType', 'tags', 'noteContacts.contact', 'attachments']);

        return [
            'id' => $note->id,
            'title' => $note->title,
            'body' => $note->body,
            'body_excerpt' => Str::length($note->body) >= 200
                ? Str::limit($note->body, 200)
                : null,
            'noted_at' => DateHelper::format($note->noted_at, $user),
            'noted_at_raw' => $note->noted_at->format('Y-m-d'),
            'created_at' => DateHelper::format($note->created_at, $user),
            'note_type' => $note->noteType ? [
                'id' => $note->noteType->id,
                'name' => $note->noteType->name,
                'display_name' => $note->noteType->display_name,
            ] : null,
            'primary_contact' => $note->primaryContact ? [
                'id' => $note->primaryContact->id,
                'name' => $note->primaryContact->name,
            ] : null,
            'contacts' => $note->noteContacts->map(fn ($nc) => [
                'id' => $nc->contact->id,
                'name' => $nc->contact->name,
                'role' => $nc->role->value,
            ])->values()->toArray(),
            'tags' => $note->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'display_name' => $tag->display_name,
            ])->values()->toArray(),
            'attachments' => $note->attachments->map(fn ($a) => [
                'id' => $a->id,
                'file_path' => $a->file_path,
                'file_type' => $a->file_type,
                'original_name' => $a->original_name,
            ])->values()->toArray(),
            'url' => [
                'show' => route('crm.notes.show', [
                    'vault' => $vaultId,
                    'note' => $note->id,
                ]),
                'update' => route('crm.notes.update', [
                    'vault' => $vaultId,
                    'note' => $note->id,
                ]),
                'destroy' => route('crm.notes.destroy', [
                    'vault' => $vaultId,
                    'note' => $note->id,
                ]),
            ],
        ];
    }
}
