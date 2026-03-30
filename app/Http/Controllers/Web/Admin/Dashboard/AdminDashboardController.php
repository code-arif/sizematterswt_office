<?php

namespace App\Http\Controllers\Web\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Farm;
use App\Models\Favorite;
use App\Models\Ranche;
use App\Models\User;
use App\Models\VisitedPlace;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ── Stat Cards ─────────────────────────────────────────────────────
        $totalFarms   = Farm::where('status', 'active')->count();
        $totalRanches = Ranche::where('status', 'active')->count();
        $totalEvents  = Event::whereIn('status', ['upcoming', 'ongoing'])->count();
        $totalUsers   = User::where('status', 'active')->count();

        // Growth % vs last month
        $farmGrowth   = $this->growthPercent(Farm::class,   'status', 'active');
        $rancheGrowth = $this->growthPercent(Ranche::class,  'status', 'active');
        $eventGrowth  = $this->growthPercent(Event::class,  null,     null);
        $userGrowth   = $this->growthPercent(User::class,   'status', 'active');

        // ── Monthly Registrations Chart (last 12 months) ───────────────────
        $months = collect(range(11, 0))->map(fn($i) => Carbon::now()->subMonths($i));

        $userRegistrations = User::selectRaw('YEAR(created_at) as yr, MONTH(created_at) as mo, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . $r->mo);

        $farmCreations = Farm::selectRaw('YEAR(created_at) as yr, MONTH(created_at) as mo, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . $r->mo);

        $eventCreations = Event::selectRaw('YEAR(created_at) as yr, MONTH(created_at) as mo, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . $r->mo);

        $chartLabels       = $months->map(fn($m) => $m->format('M Y'))->values()->toJson();
        $chartUsers        = $months->map(fn($m) => $userRegistrations->get($m->year . '-' . $m->month)?->total ?? 0)->values()->toJson();
        $chartFarms        = $months->map(fn($m) => $farmCreations->get($m->year . '-' . $m->month)?->total ?? 0)->values()->toJson();
        $chartEvents       = $months->map(fn($m) => $eventCreations->get($m->year . '-' . $m->month)?->total ?? 0)->values()->toJson();

        // Summary totals for the sub-header inside the chart card
        $chartTotalUsers  = User::count();
        $chartTotalFarms  = Farm::count();
        $chartTotalVisits = VisitedPlace::count();
        $chartTotalFavs   = Favorite::count();

        // ── Listings by State (for progress bars) ─────────────────────────
        $topStatesFarms = Farm::selectRaw('state, COUNT(*) as total')
            ->whereNotNull('state')
            ->groupBy('state')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topStatesRanches = Ranche::selectRaw('state, COUNT(*) as total')
            ->whereNotNull('state')
            ->groupBy('state')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $maxState = max(
            $topStatesFarms->max('total') ?? 1,
            $topStatesRanches->max('total') ?? 1
        );

        // ── Type Distribution (pie chart) ─────────────────────────────────
        $pieFarms   = Farm::count();
        $pieRanches = Ranche::count();
        $pieEvents  = Event::count();
        $pieTotal   = max($pieFarms + $pieRanches + $pieEvents, 1);

        // ── Recent Farms (replaces "Best Selling Products") ───────────────
        $recentFarms = Farm::with('admin')
            ->latest()
            ->limit(5)
            ->get();

        // ── Upcoming Events (replaces "Top Sellers") ──────────────────────
        $upcomingEvents = Event::whereIn('status', ['upcoming', 'ongoing'])
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // ── Recent User Registrations (replaces "Recent Orders") ──────────
        $recentUsers = User::with('profile')
            ->latest()
            ->limit(7)
            ->get();

        // ── Most Visited Places ────────────────────────────────────────────
        $mostVisited = VisitedPlace::selectRaw('visitable_type, visitable_id, COUNT(*) as visit_count')
            ->groupBy('visitable_type', 'visitable_id')
            ->orderByDesc('visit_count')
            ->limit(5)
            ->get()
            ->map(function ($v) {
                $model = $v->visitable_type::find($v->visitable_id);
                return [
                    'name'        => $model?->name ?? $model?->title ?? '—',
                    'type'        => class_basename($v->visitable_type),
                    'visit_count' => $v->visit_count,
                    'thumbnail'   => $model?->thumbnail ?? $model?->image ?? null,
                ];
            });

        return view('web.dashboard.dashboard', compact(
            // Cards
            'totalFarms',
            'totalRanches',
            'totalEvents',
            'totalUsers',
            'farmGrowth',
            'rancheGrowth',
            'eventGrowth',
            'userGrowth',

            // Chart
            'chartLabels',
            'chartUsers',
            'chartFarms',
            'chartEvents',
            'chartTotalUsers',
            'chartTotalFarms',
            'chartTotalVisits',
            'chartTotalFavs',

            // State bars
            'topStatesFarms',
            'topStatesRanches',
            'maxState',

            // Pie
            'pieFarms',
            'pieRanches',
            'pieEvents',
            'pieTotal',

            // Tables
            'recentFarms',
            'upcomingEvents',
            'recentUsers',
            'mostVisited'
        ));
    }

    // ── Helper: growth % this month vs last month ──────────────────────────
    private function growthPercent(string $model, ?string $col, ?string $val): array
    {
        $q = fn($start, $end) => $model::whereBetween('created_at', [$start, $end])
            ->when($col && $val, fn($q) => $q->where($col, $val))
            ->count();

        $thisMonth = $q(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $lastMonth = $q(Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth());

        if ($lastMonth === 0) {
            return ['value' => $thisMonth > 0 ? 100 : 0, 'up' => true];
        }

        $pct = round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
        return ['value' => abs($pct), 'up' => $pct >= 0];
    }
}
