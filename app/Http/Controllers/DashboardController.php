<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Inventory;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $eventsThisMonth = Event::whereBetween('date', [$startOfMonth, $endOfMonth])->count();

        $inventoryAlerts = Inventory::where('boxes', '<=', 2)->count();

        $chartDataPacena = Cache::remember('dashboard_chart_Paceña_620cc', 300, fn () => $this->getProductChartData('Paceña_620cc'));
        $chartDataHuari = Cache::remember('dashboard_chart_Huari_620cc', 300, fn () => $this->getProductChartData('Huari_620cc'));

        return view('dashboard', compact(
            'eventsThisMonth',
            'inventoryAlerts',
            'chartDataPacena',
            'chartDataHuari',
        ));
    }

    private function getProductChartData(string $productName): array
    {
        $eventIds = Event::where('event_status', 'completed')
            ->orderBy('date', 'desc')
            ->take(4)
            ->pluck('id');

        if ($eventIds->isEmpty()) {
            return [];
        }

        $sales = SaleItem::selectRaw('sales.event_id_new, COALESCE(SUM(sale_items.quantity), 0) as cajas')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereIn('sales.event_id_new', $eventIds)
            ->where('sale_items.name', $productName)
            ->where('sale_items.type', 'Caja')
            ->groupBy('sales.event_id_new')
            ->pluck('cajas', 'sales.event_id_new');

        $events = Event::whereIn('id', $eventIds)
            ->orderBy('date', 'asc')
            ->get(['id', 'client_name', 'event_type']);

        return $events->map(fn ($e) => [
            'name' => $e->client_name,
            'type' => $e->event_type,
            'value' => (int) ($sales[$e->id] ?? 0),
        ])->values()->toArray();
    }
}
