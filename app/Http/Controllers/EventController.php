<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('date', 'asc')->get();
        $eventTypes = Config::getEventTypes();

        $eventsJson = $events->map(function ($e) {
            return [
                'id' => $e->id,
                'title' => $e->client_name,
                'date' => Carbon::parse($e->date)->format('Y-m-d'),
                'type' => $e->event_type,
                'guests' => $e->guests,
                'balance' => $e->balance_pending,
                'signed' => $e->signed_contract_url,
            ];
        });

        return view('events.index', compact('events', 'eventTypes', 'eventsJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_id' => 'required|string|max:50',
            'client_phone' => 'nullable|string|max:20',
            'event_type' => 'required|string',
            'date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'advance_payment' => 'required|numeric|min:0',
        ]);

        $validated['payment_due_date'] = Carbon::parse($validated['date'])->subDay()->toDateString();
        $validated['balance_pending'] = $validated['total_amount'] - $validated['advance_payment'];
        $validated['status'] = 'Pendiente';
        $validated['payment_status'] = $validated['balance_pending'] > 0 ? 'pending' : 'paid';
        $validated['event_status'] = 'upcoming';
        $validated['registered_by'] = Auth::user()->name;

        Event::create($validated);

        return redirect()->route('events.index')->with('success', 'Evento registrado exitosamente.');
    }

    public function show(Event $event)
    {
        return view('events.partials.detail', compact('event'))->render();
    }

    public function editData(Event $event)
    {
        return response()->json([
            'client_name' => $event->client_name,
            'client_id' => $event->client_id,
            'client_phone' => $event->client_phone,
            'date' => $event->date->format('Y-m-d'),
            'event_type' => $event->event_type,
            'total_amount' => $event->total_amount,
            'advance_payment' => $event->advance_payment,
        ]);
    }

    public function downloadCalendar()
    {
        $events = Event::where('event_status', 'upcoming')->orderBy('date')->get();

        $html = view('pdf.calendar', compact('events'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('Calendario_Eventos_'.date('Y-m-d').'.pdf');
    }

    public function downloadReport(Request $request)
    {
        $query = Event::orderBy('date');

        if ($request->filled('year') && ! $request->filled('month')) {
            $query->whereYear('date', $request->year);
        }

        if ($request->filled('month')) {
            $year = $request->filled('year') ? $request->year : now()->year;
            $query->whereYear('date', $year)->whereMonth('date', $request->month);
        }

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $events = $query->get();

        $title = match (true) {
            $request->filled('from') && $request->filled('to') => 'Reporte del '.date('d/m/Y', strtotime($request->from)).' al '.date('d/m/Y', strtotime($request->to)),
            $request->filled('month') => 'Reporte - '.strtoupper(now()->month((int) $request->month)->translatedFormat('F')).' '.$year,
            $request->filled('year') => 'Reporte - AÑO '.$request->year,
            default => 'Reporte de Eventos',
        };

        $html = view('pdf.report', compact('events', 'title'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('Reporte_Eventos_'.date('Y-m-d').'.pdf');
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'client_name' => 'string|max:255',
            'client_id' => 'string|max:50',
            'client_phone' => 'nullable|string|max:20',
            'event_type' => 'string',
            'date' => 'date',
            'total_amount' => 'numeric|min:0',
            'advance_payment' => 'numeric|min:0',
            'payment_status' => 'string',
            'event_status' => 'string',
            'signed_contract_url' => 'nullable|string',
        ]);

        if (isset($validated['date'])) {
            $validated['payment_due_date'] = Carbon::parse($validated['date'])->subDay()->toDateString();
        }

        if (isset($validated['total_amount']) || isset($validated['advance_payment'])) {
            $totalAmount = $validated['total_amount'] ?? $event->total_amount;
            $advancePayment = $validated['advance_payment'] ?? $event->advance_payment;
            $validated['balance_pending'] = $totalAmount - $advancePayment;
            if (! isset($validated['payment_status'])) {
                $validated['payment_status'] = $validated['balance_pending'] > 0 ? 'pending' : 'paid';
            }
        }

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Evento actualizado.');
    }

    public function destroy(Event $event)
    {
        $event->update(['event_status' => 'cancelled']);
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento eliminado.');
    }

    public function payBalance(Request $request, Event $event)
    {
        $event->update([
            'advance_payment' => $event->total_amount,
            'balance_pending' => 0,
            'payment_status' => 'paid',
        ]);

        return back()->with('success', 'Saldo pagado completamente.');
    }

    public function uploadContract(Request $request, Event $event)
    {
        $request->validate([
            'contract_file' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('contract_file')) {
            $path = $request->file('contract_file')->store('contracts', 'local');
            $event->update(['signed_contract_url' => $path]);
        } else {
            $event->update(['signed_contract_url' => 'simulated-storage/contract.pdf']);
        }

        return back()->with('success', 'Contrato subido exitosamente.');
    }

    public function close(Event $event)
    {
        $event->update(['event_status' => 'completed']);

        return back()->with('success', 'Evento cerrado.');
    }

    public function manageTypes(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->validate(['new_type' => 'required|string']);
            $types = Config::getEventTypes();
            $newType = $request->new_type;

            if (! in_array($newType, $types)) {
                $types[] = $newType;
                Config::setEventTypes($types);
            }
        }

        if ($request->isMethod('DELETE')) {
            $types = Config::getEventTypes();
            $types = array_filter($types, fn ($t) => $t !== $request->type);
            Config::setEventTypes(array_values($types));
        }

        return back();
    }

    public function downloadContract(Event $event)
    {
        $settings = Config::getContractSettings();
        $watermarkPath = Config::getWatermark();
        $watermark = '';

        if ($watermarkPath && file_exists(storage_path('app/public/'.$watermarkPath))) {
            $imageData = file_get_contents(storage_path('app/public/'.$watermarkPath));
            $mime = mime_content_type(storage_path('app/public/'.$watermarkPath));
            $watermark = 'data:'.$mime.';base64,'.base64_encode($imageData);
        }

        $html = view('pdf.contract', compact('event', 'settings', 'watermark'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download("Contrato_{$event->client_name}.pdf");
    }
}
