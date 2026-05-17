<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('date', 'desc')->get();
        $closedEvents = $events->where('status', 'Cerrado');
        
        $totalSales = 0;
        foreach ($closedEvents as $event) {
            $sales = Sale::where('event_id', $event->id)->get();
            $totalSales += $sales->sum('amount');
        }

        return view('reports.index', compact('events', 'closedEvents', 'totalSales'));
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
        $sales = Sale::where('event_id', $event->id)->orderBy('id', 'desc')->get();
        $totalAmount = $sales->sum('amount');
        
        $html = view('pdf.report', compact('event', 'sales', 'totalAmount'))->render();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        return $pdf->download("Reporte_{$event->client_name}.pdf");
    }
}