<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseAPIController;
use App\Models\Brt;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseAPIController
{
    public function index()
    {
        // Get basic statistics
        $stats = [
            'total_brts' => Brt::count(),
            'active_brts' => Brt::where('status', 'active')->count(),
            'expired_brts' => Brt::where('status', 'expired')->count(),
            'total_reserved' => Brt::sum('reserved_amount'),
        ];

        // Get BRTs created per period
        $timeRanges = $this->getTimeRangeStats();

        return view('admin.dashboard', compact('stats', 'timeRanges'));
    }


    private function getTimeRangeStats()
    {
        $now = Carbon::now();
        
        return [
            'daily' => Brt::whereBetween('created_at', [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay()
            ])->count(),
            
            'weekly' => Brt::whereBetween('created_at', [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek()
            ])->count(),
            
            'monthly' => Brt::whereBetween('created_at', [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth()
            ])->count(),
        ];
    }


    public function getChartData()
    {
        // Get daily BRT creation counts for the last 30 days
        $dailyData = Brt::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [
                Carbon::now()->subDays(30),
                Carbon::now()
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get monthly reserved amounts
        $monthlyReserved = Brt::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(reserved_amount) as total')
        )
            ->whereBetween('created_at', [
                Carbon::now()->subMonths(6),
                Carbon::now()
            ])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'daily_creation' => $dailyData,
            'monthly_reserved' => $monthlyReserved
        ]);
    }

}
