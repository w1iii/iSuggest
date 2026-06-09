<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuggestionRequest;
use App\Models\Suggestion;

class SubmitController extends Controller
{
    public function store(StoreSuggestionRequest $request)
    {
        $suggestion = Suggestion::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Suggestion saved successfully.',
            'data'    => $suggestion,
        ], 201);
    }
}
