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
            'active' => Suggestion::where('user_id', $userId)->where('status', 'Pending')->count(),
            'approved' => Suggestion::where('user_id', $userId)->where('status', 'Approved')->count(),
            'total' => Suggestion::where('user_id', $userId)->count(),
        ];

        return response()->json($stats);
    }
}
