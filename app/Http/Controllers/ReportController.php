<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $allClosedEvents = Event::where('event_status', 'completed')
            ->orderBy('date', 'desc')
            ->get();

        $closedEvents = Event::where('event_status', 'completed')
            ->withSum('sales as event_total', 'amount')
            ->withCount('sales as sales_count')
            ->orderBy('date', 'desc')
            ->paginate(4);

        $eventIds = $allClosedEvents->pluck('id');

        $totals = Sale::whereIn('event_id_new', $eventIds)
            ->selectRaw('COALESCE(SUM(amount), 0) as total')
            ->selectRaw('COALESCE(SUM(CASE WHEN payment_method = ? THEN amount END), 0) as efectivo', ['Efectivo'])
            ->selectRaw('COALESCE(SUM(CASE WHEN payment_method = ? THEN amount END), 0) as qr', ['QR'])
            ->selectRaw('COALESCE(SUM(CASE WHEN payment_method = ? THEN amount END), 0) as tarjeta', ['Tarjeta'])
            ->first();

        $totalSales = $totals->total;
        $totalEfectivo = $totals->efectivo;
        $totalQR = $totals->qr;
        $totalTarjeta = $totals->tarjeta;

        $lastEvent = $allClosedEvents->sortByDesc('updated_at')->first();
        $lastEventSales = collect();
        $lastEventSalesCount = 0;
        $lastEventBiggestSale = 0;
        $lastEventBiggestSaleClient = '';
        $lastEventEfectivo = 0;
        $lastEventQR = 0;
        $lastEventTarjeta = 0;
        $totalBoxes = 0;
        $totalUnits = 0;
        $productPercentages = [];
        $productDetails = [];

        if ($lastEvent) {
            $lastEventSales = Sale::where('event_id_new', $lastEvent->id)->with('items')->get();
            $lastEventSalesCount = $lastEventSales->count();
            $lastEventBiggestSale = $lastEventSales->max('amount') ?? 0;
            $biggestSale = $lastEventSales->firstWhere('amount', $lastEventBiggestSale);
            $lastEventBiggestSaleClient = $biggestSale?->client_name ?? '—';
            $lastEventEfectivo = $lastEventSales->where('payment_method', 'Efectivo')->sum('amount');
            $lastEventQR = $lastEventSales->where('payment_method', 'QR')->sum('amount');
            $lastEventTarjeta = $lastEventSales->where('payment_method', 'Tarjeta')->sum('amount');

            $productCounts = [];
            $totalItems = 0;
            foreach ($lastEventSales as $sale) {
                foreach ($sale->items as $item) {
                    $key = $item->name;
                    $productCounts[$key] = ($productCounts[$key] ?? 0) + $item->quantity;
                    $totalItems += $item->quantity;
                    if ($item->type === 'Caja') {
                        $totalBoxes += $item->quantity;
                    } else {
                        $totalUnits += $item->quantity;
                    }
                    if (! isset($productDetails[$key])) {
                        $productDetails[$key] = ['name' => $key, 'boxes' => 0, 'units' => 0];
                    }
                    if ($item->type === 'Caja') {
                        $productDetails[$key]['boxes'] += $item->quantity;
                    } else {
                        $productDetails[$key]['units'] += $item->quantity;
                    }
                }
            }
            foreach ($productCounts as $name => $count) {
                $productPercentages[] = [
                    'name' => $name,
                    'count' => $count,
                    'percentage' => $totalItems > 0 ? round(($count / $totalItems) * 100, 1) : 0,
                ];
            }
            usort($productPercentages, fn ($a, $b) => $b['count'] <=> $a['count']);
        }

        $directSales = Sale::where('is_direct_sale', true)->with('items')->latest()->get();
        $directSalesTotal = $directSales->sum('amount');
        $directSalesEfectivo = $directSales->where('payment_method', 'Efectivo')->sum('amount');
        $directSalesQR = $directSales->where('payment_method', 'QR')->sum('amount');
        $directSalesTarjeta = $directSales->where('payment_method', 'Tarjeta')->sum('amount');

        $directSalesByDate = $directSales->groupBy(fn ($s) => $s->created_at->format('Y-m-d'))
            ->sortKeysDesc()
            ->map(fn ($group) => [
                'sales' => $group,
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ]);

        return view('reports.index', compact(
            'closedEvents', 'totalSales', 'totalEfectivo', 'totalQR', 'totalTarjeta',
            'lastEvent', 'lastEventSales', 'lastEventSalesCount', 'lastEventBiggestSale', 'lastEventBiggestSaleClient',
            'lastEventEfectivo', 'lastEventQR', 'lastEventTarjeta', 'productPercentages',
            'totalBoxes', 'totalUnits', 'productDetails',
            'directSales', 'directSalesTotal', 'directSalesEfectivo', 'directSalesQR', 'directSalesTarjeta',
            'directSalesByDate'
        ));
    }

    public function show(Event $event)
    {
        $totalAmount = Sale::where('event_id_new', $event->id)->sum('amount');
        $sales = Sale::where('event_id_new', $event->id)->with('items')->orderBy('id', 'desc')->paginate(7);

        return view('reports.show', compact('event', 'sales', 'totalAmount'));
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('reports.index')->with('success', 'Evento eliminado.');
    }

    public function downloadPdf(Event $event)
    {
        $sales = Sale::where('event_id_new', $event->id)->with('items')->orderBy('id', 'desc')->get();
        $totalAmount = $sales->sum('amount');

        $html = view('pdf.event-detail', compact('event', 'sales', 'totalAmount'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download("Reporte_{$event->client_name}.pdf");
    }

    public function directSales(string $date)
    {
        $sales = Sale::where('is_direct_sale', true)
            ->whereDate('created_at', $date)
            ->with('items')
            ->latest()
            ->get();

        $total = $sales->sum('amount');
        $efectivo = $sales->where('payment_method', 'Efectivo')->sum('amount');
        $qr = $sales->where('payment_method', 'QR')->sum('amount');
        $tarjeta = $sales->where('payment_method', 'Tarjeta')->sum('amount');

        $displayDate = Carbon::parse($date)->format('d/m/Y');

        return view('reports.direct', compact('sales', 'total', 'efectivo', 'qr', 'tarjeta', 'displayDate', 'date'));
    }

    public function directSalesPdf(string $date)
    {
        $sales = Sale::where('is_direct_sale', true)
            ->whereDate('created_at', $date)
            ->with('items')
            ->latest()
            ->get();

        $total = $sales->sum('amount');
        $efectivo = $sales->where('payment_method', 'Efectivo')->sum('amount');
        $qr = $sales->where('payment_method', 'QR')->sum('amount');
        $tarjeta = $sales->where('payment_method', 'Tarjeta')->sum('amount');
        $displayDate = Carbon::parse($date)->format('d/m/Y');

        $html = view('pdf.direct-sales', compact('sales', 'total', 'efectivo', 'qr', 'tarjeta', 'displayDate', 'date'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download("Ventas_Directas_{$date}.pdf");
    }
}
