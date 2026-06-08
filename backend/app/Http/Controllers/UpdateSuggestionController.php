<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion; // Remember, we are using the Suggestion model!

class UpdateSuggestionController extends Controller
{
    // 1. UPDATE a suggestion
    public function update(Request $request, $id)
    {
        // Find the suggestion, but ONLY if it belongs to the logged-in user
        $suggestion = Suggestion::where('user_id', auth()->id())->where('id', $id)->first();

        if (!$suggestion) {
            return response()->json([
                'success' => false,
                'message' => 'Suggestion not found or unauthorized'
            ], 404);
        }

        // Update the fields (if the user didn't send a new title, keep the old one)
        $suggestion->title = $request->input('title', $suggestion->title);
        $suggestion->description = $request->input('description', $suggestion->description);
        $suggestion->category = $request->input('category', $suggestion->category);
        $suggestion->save();

        return response()->json([
            'success' => true,
            'message' => 'Suggestion updated successfully',
            'data' => $suggestion
        ], 200);
    }

  
}
