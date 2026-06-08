<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion;

class DeleteSuggestionController extends Controller
{

    // 2. DELETE a suggestion
    public function destroy($id)
    {
        // Find the suggestion, but ONLY if it belongs to the logged-in user
        $suggestion = Suggestion::where('user_id', auth()->id())->where('id', $id)->first();

        if (!$suggestion) {
            return response()->json([
                'success' => false,
                'message' => 'Suggestion not found or unauthorized'
            ], 404);
        }

        $suggestion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Suggestion deleted successfully'
        ], 200);
    }

}
