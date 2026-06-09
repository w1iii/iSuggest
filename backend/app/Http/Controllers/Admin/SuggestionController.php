<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function statuses()
    {
        return response()->json([
            'statuses' => config('suggestions.statuses'),
        ]);
    }

    public function index(Request $request)
    {
        $query = Suggestion::with('user:id,name,email');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $suggestions = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($suggestions);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', config('suggestions.statuses'))],
            'admin_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $suggestion = Suggestion::findOrFail($id);
        $suggestion->update($validated);

        return response()->json([
            'message' => 'Suggestion status updated.',
            'data' => $suggestion->load('user:id,name,email'),
        ]);
    }
}
