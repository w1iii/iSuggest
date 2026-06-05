<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion; 

class GetSuggestionsController extends Controller
{
    public function get(Request $request)
    {
        $user = auth()->id();

       $suggestions = Suggestion::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Suggestions retrieved successfully',
            'data' => $suggestions
        ], 201);
    }
}