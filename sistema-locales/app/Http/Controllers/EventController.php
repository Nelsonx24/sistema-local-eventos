<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('date', 'asc')->get();
        $eventTypes = Config::getEventTypes();
        
        $eventsJson = $events->map(function($e) {
            return [
                'id' => $e->id,
                'title' => $e->client_name,
                'date' => \Carbon\Carbon::parse($e->date)->format('Y-m-d'),
                'type' => $e->event_type,
                'guests' => $e->guests,
                'balance' => $e->balance_pending,
                'signed' => $e->signed_contract_url
            ];
        });
        
        return view('events.index', compact('events', 'eventTypes', 'eventsJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_id' => 'required|string|max:50',
            'event_type' => 'required|string',
            'date' => 'required|date',
            'guests' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'advance_payment' => 'required|numeric|min:0',
            'payment_due_date' => 'required|date',
        ]);

        $validated['balance_pending'] = $validated['total_amount'] - $validated['advance_payment'];
        $validated['status'] = 'Pendiente';
        $validated['seller_name'] = Auth::user()->name;

        Event::create($validated);

        return redirect()->route('events.index')->with('success', 'Evento registrado exitosamente.');
    }

    public function show(Event $event)
    {
        return view('events.partials.detail', compact('event'))->render();
    }

    public function downloadCalendar()
    {
        $events = Event::orderBy('date')->get();
        
        $html = view('pdf.calendar', compact('events'))->render();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        return $pdf->download("Calendario_Eventos_" . date('Y-m-d') . ".pdf");
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'client_name' => 'string|max:255',
            'client_id' => 'string|max:50',
            'event_type' => 'string',
            'date' => 'date',
            'guests' => 'integer|min:1',
            'total_amount' => 'numeric|min:0',
            'advance_payment' => 'numeric|min:0',
            'payment_due_date' => 'date',
            'status' => 'string',
            'signed_contract_url' => 'nullable|string',
        ]);

        if (isset($validated['total_amount']) || isset($validated['advance_payment'])) {
            $totalAmount = $validated['total_amount'] ?? $event->total_amount;
            $advancePayment = $validated['advance_payment'] ?? $event->advance_payment;
            $validated['balance_pending'] = $totalAmount - $advancePayment;
        }

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Evento actualizado.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Evento eliminado.');
    }

    public function payBalance(Request $request, Event $event)
    {
        $event->update([
            'advance_payment' => $event->total_amount,
            'balance_pending' => 0,
            'status' => 'Confirmado',
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
        $event->update(['status' => 'Cerrado']);
        return back()->with('success', 'Evento cerrado.');
    }

    public function manageTypes(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->validate(['new_type' => 'required|string']);
            $types = Config::getEventTypes();
            $newType = $request->new_type;
            
            if (!in_array($newType, $types)) {
                $types[] = $newType;
                Config::setEventTypes($types);
            }
        }

        if ($request->isMethod('DELETE')) {
            $types = Config::getEventTypes();
            $types = array_filter($types, fn($t) => $t !== $request->type);
            Config::setEventTypes(array_values($types));
        }

        return back();
    }

    public function downloadContract(Event $event)
    {
        $settings = Config::getContractSettings();
        
        $html = view('pdf.contract', compact('event', 'settings'))->render();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        return $pdf->download("Contrato_{$event->client_name}.pdf");
    }
}