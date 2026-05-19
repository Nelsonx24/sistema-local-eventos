<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('date', 'desc')->get();
        $closedEvents = $events->where('event_status', 'completed');

        $totalSales = 0;
        $totalEfectivo = 0;
        $totalQR = 0;
        $totalTarjeta = 0;
        foreach ($closedEvents as $event) {
            $sales = Sale::where('event_id', $event->id)->get();
            $totalSales += $sales->sum('amount');
            $totalEfectivo += $sales->where('payment_method', 'Efectivo')->sum('amount');
            $totalQR += $sales->where('payment_method', 'QR')->sum('amount');
            $totalTarjeta += $sales->where('payment_method', 'Tarjeta')->sum('amount');
        }

        $lastEvent = $closedEvents->sortByDesc('updated_at')->first();
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
            $lastEventSales = Sale::where('event_id', $lastEvent->id)->with('items')->get();
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

        $directSales = Sale::where('event_id', 'like', 'Venta Directa%')->with('items')->latest()->get();
        $directSalesTotal = $directSales->sum('amount');
        $directSalesEfectivo = $directSales->where('payment_method', 'Efectivo')->sum('amount');
        $directSalesQR = $directSales->where('payment_method', 'QR')->sum('amount');
        $directSalesTarjeta = $directSales->where('payment_method', 'Tarjeta')->sum('amount');

        return view('reports.index', compact(
            'events', 'closedEvents', 'totalSales', 'totalEfectivo', 'totalQR', 'totalTarjeta',
            'lastEvent', 'lastEventSales', 'lastEventSalesCount', 'lastEventBiggestSale', 'lastEventBiggestSaleClient',
            'lastEventEfectivo', 'lastEventQR', 'lastEventTarjeta', 'productPercentages',
            'totalBoxes', 'totalUnits', 'productDetails',
            'directSales', 'directSalesTotal', 'directSalesEfectivo', 'directSalesQR', 'directSalesTarjeta'
        ));
    }

    public function show(Event $event)
    {
        $sales = Sale::where('event_id', $event->id)->orderBy('id', 'desc')->get();
        $totalAmount = $sales->sum('amount');

        return view('reports.show', compact('event', 'sales', 'totalAmount'));
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('reports.index')->with('success', 'Evento eliminado.');
    }

    public function getEventTotal(string $eventId): float
    {
        return Sale::where('event_id', $eventId)->sum('amount');
    }

    public function downloadPdf(Event $event)
    {
        $events = collect([$event]);
        $title = 'Reporte - '.$event->client_name;

        $html = view('pdf.report', compact('events', 'title'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download("Reporte_{$event->client_name}.pdf");
    }
}
