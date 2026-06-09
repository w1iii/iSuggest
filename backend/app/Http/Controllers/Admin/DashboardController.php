<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_ideas' => Suggestion::count(),
            'in_review' => Suggestion::where('status', 'In Review')->count(),
            'implemented' => Suggestion::where('status', 'Implemented')->count(),
            'goal' => 200,
            'growth_pct' => 12,
        ]);
    }

    public function activity()
    {
        $suggestions = Suggestion::with('user:id,name')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($s) {
                $type = match (true) {
                    $s->status === 'Implemented' => 'promoted',
                    $s->status === 'Approved' => 'approved',
                    $s->status === 'In Review' => 'review',
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

    public function topIdeas()
    {
        $ideas = Suggestion::with('user:id,name')
            ->where('status', 'Implemented')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'title' => $s->title,
                    'description' => $s->description,
                    'category' => $s->category,
                    'status' => $s->status,
                    'score' => 100,
                    'user_name' => $s->user?->name ?? 'Unknown',
                    'created_at' => $s->created_at,
                ];
            });

        return response()->json($ideas);
    }

    public function trendsByPeriod($days = 30)
    {
        $trends = [];
        $totalDays = (int) $days;

        for ($i = $totalDays - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $nextDate = $date->copy()->endOfDay();

            $count = Suggestion::whereBetween('created_at', [$date, $nextDate])->count();
            $trends[] = [
                'date' => $date->format('Y-m-d'),
                'day_label' => $date->format('D'),
                'count' => $count,
            ];
        }

        return response()->json($trends);
    }

    public function downloadReport()
    {
        $stats = [
            'total_ideas' => Suggestion::count(),
            'in_review' => Suggestion::where('status', 'In Review')->count(),
            'implemented' => Suggestion::where('status', 'Implemented')->count(),
            'growth_pct' => 12,
        ];

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

        $topIdeas = Suggestion::with('user:id,name')
            ->where('status', 'Implemented')
            ->latest()
            ->take(10)
            ->get();

        $pdf = Pdf::loadView('reports.analytics', compact('stats', 'categories', 'topIdeas'));
        return $pdf->download('analytics-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
