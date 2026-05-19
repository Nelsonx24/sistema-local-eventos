<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Staff;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $eventsThisMonth = Event::whereBetween('date', [$startOfMonth, $endOfMonth])->count();

        $salesThisMonth = Sale::whereBetween('date', [$startOfMonth, $endOfMonth])->get();
        $monthlyRevenue = $salesThisMonth->sum('amount');

        $inventoryAlerts = Inventory::where('boxes', '<=', 2)->count();
        $staffCount = Staff::where('status', 'Active')->count();

        $chartData = $this->getWeeklySalesData();

        return view('dashboard', compact(
            'monthlyRevenue',
            'eventsThisMonth',
            'inventoryAlerts',
            'staffCount',
            'chartData'
        ));
    }

    private function getWeeklySalesData()
    {
        $weeks = [];
        $now = Carbon::now();

        for ($i = 3; $i >= 0; $i--) {
            $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $sales = Sale::whereBetween('date', [$weekStart, $weekEnd])->get();
            $total = $sales->sum('amount');

            $weeks[] = [
                'name' => 'Sem '.(4 - $i),
                'value' => $total,
            ];
        }

        return $weeks;
    }
}
