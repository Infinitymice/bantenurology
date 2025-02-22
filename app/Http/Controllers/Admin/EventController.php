<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;
use Carbon\Carbon; 

class EventController extends Controller
{
    public function getEventsData()
    {
        $events = Event::with('eventType')->select('events.*');
        return datatables()->of($events)
            ->addColumn('event_type', function ($row) {
                return $row->eventType->name ?? '-';
            })
            ->addColumn('early_bid_date', function ($row) {
                return Carbon::parse($row->early_bid_date)->isoFormat('D MMMM YYYY');
            })
            ->addColumn('event_date', function ($row) {
                return Carbon::parse($row->event_date)->isoFormat('D MMMM YYYY');
            })
            ->addColumn('event_date_day2', function ($row) {
                return $row->event_date_day2 
                ? Carbon::parse($row->event_date_day2)->isoFormat('D MMMM YYYY') 
                : '-';
            })
            ->addColumn('kuota', function ($row) {
                return isset($row->kuota) ? $row->kuota : 'Kuota tidak diset';
            })
            
            ->addColumn('actions', function ($row) {
                return '
                    <a href="'.route('admin.events.edit', $row->id).'" class="btn btn-sm btn-warning">Edit</a>
                    <form action="'.route('admin.events.delete', $row->id).'" method="POST" style="display:inline-block;">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                    </form>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    // Menampilkan halaman index untuk Events
    public function index()
    {
        return view('admin.events.index');
    }

    // Menampilkan halaman create untuk Events
    public function create()
    {
        $eventTypes = EventType::all(); 
        return view('admin.events.create', compact('eventTypes'));
    }

    // Menyimpan Event baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'early_bid_price' => 'required|numeric',
            'onsite_price' => 'required|numeric',
            'early_bid_date' => 'required|date', 
            'event_date' => 'required|date',
            'event_date_day2' => 'nullable|date',
            'kuota' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();

        // Set `kuota` ke null jika checkbox tidak dicentang
        if (!$request->has('kuota')) {
            $data['kuota'] = null;
        }

        Event::create($request->all());
        return redirect()->route('admin.index')->with('success', 'Event Berhasil Dibuat.');
    }

    // Menampilkan halaman edit untuk Event
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $eventTypes = EventType::all();
        return view('admin.events.edit', compact('event', 'eventTypes'));
    }

    // Mengupdate Event
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'early_bid_price' => 'required|numeric',
            'onsite_price' => 'required|numeric',
            'early_bid_date' => 'required|date', 
            'event_date' => 'required|date',
            'event_date_day2' => 'nullable|date',
            'kuota' => 'nullable|integer|min:0',
        ]);

        $event = Event::findOrFail($id);

        $data = $request->all();

        // Set `kuota` ke null jika checkbox tidak dicentang
        if (!$request->has('kuota')) {
            $data['kuota'] = null;
        }
        //dd($data);


        $event->early_bid_date = \Carbon\Carbon::parse($request->early_bid_date)->format('Y-m-d');
        $event->event_date = \Carbon\Carbon::parse($request->event_date)->format('Y-m-d');
        $event->event_date_day2 = $request->event_date_day2 ? \Carbon\Carbon::parse($request->event_date_day2)->format('Y-m-d') : null;

        $event->early_bid_price = floatval($request->early_bid_price);
        $event->onsite_price = floatval($request->onsite_price);

        // Update data lainnya
        $event->update($request->except(['early_bid_date', 'event_date', 'event_date_day2', 'early_bid_price', 'onsite_price']));

        return redirect()->route('admin.index')->with('success', 'Event Berhasil Di Update.');
    }


    // Menghapus Event
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return redirect()->route('admin.index')->with('success', 'Event Berhasil dihapus.');
    }
}
