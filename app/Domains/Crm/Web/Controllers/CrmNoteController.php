<?php

namespace App\Domains\Crm\Web\Controllers;

use App\Domains\Crm\Services\CreateCrmNote;
use App\Domains\Crm\Services\DestroyCrmNote;
use App\Domains\Crm\Services\UpdateCrmNote;
use App\Domains\Crm\Web\ViewHelpers\CrmNoteViewHelper;
use App\Domains\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Helpers\PaginatorHelper;
use App\Http\Controllers\Controller;
use App\Models\CrmNote;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CrmNoteController extends Controller
{
    /**
     * List all CRM notes for a vault (Inertia page).
     */
    public function index(Request $request, string $vaultId)
    {
        $vault = Vault::findOrFail($vaultId);

        $notes = CrmNote::forAccount(Auth::user()->account_id)
            ->with(['primaryContact', 'tags', 'noteType'])
            ->orderBy('noted_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Crm/Notes/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => CrmNoteViewHelper::index($notes, Auth::user(), $vaultId),
            'paginator' => PaginatorHelper::getData($notes),
        ]);
    }

    /**
     * Show a single CRM note (Inertia page).
     */
    public function show(Request $request, string $vaultId, string $noteId)
    {
        $vault = Vault::findOrFail($vaultId);

        $note = CrmNote::where('account_id', Auth::user()->account_id)
            ->findOrFail($noteId);

        return Inertia::render('Crm/Notes/Show', [
            'layoutData' => VaultIndexViewHelper::layoutData($vault),
            'data' => CrmNoteViewHelper::dto($note, Auth::user(), $vaultId),
        ]);
    }

    /**
     * Store a new CRM note (JSON response for inline creation).
     */
    public function store(Request $request, string $vaultId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'contact_id' => $request->input('contact_id'),
            'note_type_id' => $request->input('note_type_id'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'noted_at' => $request->input('noted_at'),
        ];

        $note = (new CreateCrmNote)->execute($data);

        return response()->json([
            'data' => CrmNoteViewHelper::dto($note, Auth::user(), $vaultId),
        ], 201);
    }

    /**
     * Update an existing CRM note (JSON response).
     */
    public function update(Request $request, string $vaultId, string $noteId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'crm_note_id' => $noteId,
            'contact_id' => $request->input('contact_id'),
            'note_type_id' => $request->input('note_type_id'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'noted_at' => $request->input('noted_at'),
        ];

        $note = (new UpdateCrmNote)->execute($data);

        return response()->json([
            'data' => CrmNoteViewHelper::dto($note, Auth::user(), $vaultId),
        ], 200);
    }

    /**
     * Delete a CRM note (JSON response).
     */
    public function destroy(Request $request, string $vaultId, string $noteId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::id(),
            'vault_id' => $vaultId,
            'crm_note_id' => $noteId,
        ];

        (new DestroyCrmNote)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
