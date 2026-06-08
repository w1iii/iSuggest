<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use Illuminate\Http\Request;

class SuggestionsController extends Controller
{
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
