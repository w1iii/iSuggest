<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSuggestionRequest;
use App\Models\Suggestion;

class UpdateSuggestionController extends Controller
{
    public function update(UpdateSuggestionRequest $request, $id)
    {
        $suggestion = Suggestion::where('user_id', auth()->id())->where('id', $id)->first();

        if (!$suggestion) {
            return response()->json([
                'success' => false,
                'message' => 'Suggestion not found or unauthorized'
            ], 404);
        }

        $suggestion->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Suggestion updated successfully',
            'data' => $suggestion
        ], 200);
    }
}
