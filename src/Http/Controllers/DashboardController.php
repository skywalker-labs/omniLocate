<?php

namespace Skywalker\Location\Http\Controllers;

use Illuminate\Routing\Controller;
use Skywalker\Location\Models\GeoAnalytics;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('location::dashboard');
    }

    /**
     * Get stats for the dashboard charts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        // 1. Total Requests
        $total = GeoAnalytics::count();

        // 2. Blocked / High Risk (Assume > 70 is effectively blocked/throttled)
        $threats = GeoAnalytics::where('risk_score', '>=', 70)->count();

        // 3. Top Countries
        $topCountries = GeoAnalytics::select('country_code', DB::raw('count(*) as count'))
            ->groupBy('country_code')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // 4. Risk Distribution
        $lowRisk = GeoAnalytics::where('risk_score', '<', 30)->count();
        $medRisk = GeoAnalytics::whereBetween('risk_score', [30, 69])->count();
        $highRisk = GeoAnalytics::where('risk_score', '>=', 70)->count();

        // 5. Recent Logs
        $logs = GeoAnalytics::latest()->limit(10)->get();

        return response()->json([
            'total_requests' => $total,
            'blocked_threats' => $threats,
            'top_countries' => $topCountries,
            'risk_distribution' => [$lowRisk, $medRisk, $highRisk],
            'logs' => $logs
        ]);
    }
}

