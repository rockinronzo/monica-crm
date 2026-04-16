<?php

namespace App\Domains\Crm\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\CrmTag;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmSearchController extends Controller
{
    /**
     * Search contacts by name for @mention autocomplete.
     *
     * GET /crm/{vault}/search/contacts?q=john
     *
     * Returns up to 10 matching contacts in the vault.
     */
    public function contacts(Request $request, string $vaultId)
    {
        $vault = Vault::findOrFail($vaultId);
        $query = $request->input('q', '');

        if (strlen($query) < 1) {
            return response()->json(['data' => []], 200);
        }

        $contacts = $vault->contacts()
            ->where('listed', true)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', $query . '%')
                  ->orWhere('last_name', 'like', $query . '%')
                  ->orWhere('nickname', 'like', $query . '%');
            })
            ->limit(10)
            ->get();

        $results = $contacts->map(fn (Contact $contact) => [
            'id' => $contact->id,
            'name' => $contact->name,
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
        ]);

        return response()->json([
            'data' => $results->values()->toArray(),
        ], 200);
    }

    /**
     * Search tags by name for #hashtag autocomplete.
     *
     * GET /crm/{vault}/search/tags?q=work
     *
     * Returns up to 10 matching tags in the account.
     */
    public function tags(Request $request, string $vaultId)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 1) {
            return response()->json(['data' => []], 200);
        }

        $accountId = Auth::user()->account_id;

        $tags = CrmTag::where('account_id', $accountId)
            ->where('name', 'like', strtolower($query) . '%')
            ->limit(10)
            ->get();

        $results = $tags->map(fn (CrmTag $tag) => [
            'id' => $tag->id,
            'name' => $tag->name,
            'display_name' => $tag->display_name,
        ]);

        return response()->json([
            'data' => $results->values()->toArray(),
        ], 200);
    }
}
