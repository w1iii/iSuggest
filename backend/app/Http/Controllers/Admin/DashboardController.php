<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\User;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_ideas' => Suggestion::count(),
            'in_review' => Suggestion::where('status', 'Pending')->count(),
            'implemented' => Suggestion::where('status', 'Implemented')->count(),
            'goal' => 200,
            'growth_pct' => 12,
        ]);
    }

    public function activity()
    {
        $suggestions = Suggestion::with('user:id,name')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($s) {
                $type = match (true) {
                    $s->status === 'Implemented' => 'promoted',
                    $s->status === 'Approved' => 'approved',
                    $s->admin_remarks !== null => 'feedback',
                    default => 'new',
                };
                return [
                    'id' => $s->id,
                    'type' => $type,
                    'title' => $s->title,
                    'description' => $s->description,
                    'user_name' => $s->user?->name ?? 'Unknown',
                    'category' => $s->category,
                    'status' => $s->status,
                    'created_at' => $s->created_at,
                ];
            });

        return response()->json($suggestions);
    }

    public function categories()
    {
        $total = Suggestion::count();

        $categories = Suggestion::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) use ($total) {
                return [
                    'name' => $item->category,
                    'count' => $item->count,
                    'percentage' => $total > 0 ? round(($item->count / $total) * 100) : 0,
                ];
            });

        return response()->json($categories);
    }

    public function contributors()
    {
        $contributors = User::withCount('suggestions')
            ->having('suggestions_count', '>', 0)
            ->orderByDesc('suggestions_count')
            ->take(10)
            ->get(['id', 'name', 'email'])
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'ideas_count' => $u->suggestions_count,
                ];
            });

        return response()->json($contributors);
    }
}
