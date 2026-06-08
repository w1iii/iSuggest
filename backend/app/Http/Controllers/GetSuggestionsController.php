<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion; 

class GetSuggestionsController extends Controller
{
    public function get(Request $request)
    {
        $user = auth()->user();

        $query = Suggestion::where('user_id', $user->id)->orderBy('created_at', 'desc');

        $perPage = $request->integer('limit', 0);
        if ($perPage > 0) {
            $suggestions = $query->paginate($perPage);
        } else {
            $suggestions = $query->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Suggestions retrieved successfully',
            'data' => $suggestions
        ], 200);
    }
}