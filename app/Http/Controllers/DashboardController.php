<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;
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

        $chartDataPacena = $this->getProductChartData('Paceña_620cc');
        $chartDataHuari = $this->getProductChartData('Huari_620cc');

        return view('dashboard', compact(
            'eventsThisMonth',
            'inventoryAlerts',
            'chartDataPacena',
            'chartDataHuari'
        ));
    }

    private function getProductChartData(string $productName): array
    {
        $events = Event::where('event_status', 'completed')->orderBy('date', 'desc')->take(4)->get();

        $data = [];
        foreach ($events->reverse() as $event) {
            $saleIds = Sale::where('event_id', $event->id)->pluck('id');

            $cajas = SaleItem::whereIn('sale_id', $saleIds)
                ->where('name', $productName)
                ->where('type', 'Caja')
                ->sum('quantity');

            $data[] = [
                'name' => $event->client_name,
                'type' => $event->event_type,
                'value' => $cajas,
            ];
        }

        return $data;
    }
}
