<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion;

class GetRecentSuggestionsController extends Controller
{
    public function get(Request $request)
    {
        // Get the 3 most recent suggestions ordered by created_at (newest first)
        $suggestions = Suggestion::orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Recent suggestions retrieved successfully',
            'data' => $suggestions
        ], 200);
    }
}
