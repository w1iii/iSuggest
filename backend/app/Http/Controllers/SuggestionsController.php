<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuggestionRequest;
use App\Http\Requests\UpdateSuggestionRequest;
use App\Models\Suggestion;
use Illuminate\Http\Request;

class SuggestionsController extends Controller
{
    public function index(Request $request)
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

    public function destroy($id)
    {
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

    public function stats()
    {
        $stats = [
            'total' => Suggestion::count(),
            'pending' => Suggestion::where('status', 'Pending')->count(),
            'approved' => Suggestion::where('status', 'Approved')->count(),
            'rejected' => Suggestion::where('status', 'Rejected')->count(),
            'implemented' => Suggestion::where('status', 'Implemented')->count(),
        ];

        return response()->json($stats);
    }

    public function userStats()
    {
        $userId = auth()->id();

        $stats = [
            'total' => Suggestion::where('user_id', $userId)->count(),
            'pending' => Suggestion::where('user_id', $userId)->where('status', 'Pending')->count(),
            'approved' => Suggestion::where('user_id', $userId)->where('status', 'Approved')->count(),
            'rejected' => Suggestion::where('user_id', $userId)->where('status', 'Rejected')->count(),
            'implemented' => Suggestion::where('user_id', $userId)->where('status', 'Implemented')->count(),
        ];

        return response()->json($stats);
    }
}
