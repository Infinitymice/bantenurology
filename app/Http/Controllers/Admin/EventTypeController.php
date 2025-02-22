<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use Illuminate\Http\Request;

class EventTypeController extends Controller
{
    // Menampilkan DataTables untuk EventTypes
    public function getEventTypesData()
    {
        $eventTypes = EventType::select('id', 'name');
        return datatables()->of($eventTypes)
            ->addColumn('actions', function ($row) {
                return '
                    <a href="'.route('admin.event-types.edit', $row->id).'" class="btn btn-sm btn-warning">Edit</a>
                    <form action="'.route('admin.event-types.delete', $row->id).'" method="POST" style="display:inline-block;">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                    </form>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    // Menampilkan halaman index untuk EventTypes
    public function index()
    {
        return view('admin.event-types.index');
    }

    // Menampilkan halaman create untuk EventTypes
    public function create()
    {
        return view('admin.event-types.create');
    }

    // Menyimpan EventType baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        EventType::create($request->all());
        return redirect()->route('admin.event-types.index')->with('success', 'EventType created successfully.');
    }

    // Menampilkan halaman edit untuk EventType
    public function edit($id)
    {
        $eventType = EventType::findOrFail($id);
        return view('admin.event-types.edit', compact('eventType'));
    }

    // Mengupdate EventType
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $eventType = EventType::findOrFail($id);
        $eventType->update($request->all());
        return redirect()->route('admin.event-types.index')->with('success', 'EventType updated successfully.');
    }

    // Menghapus EventType
    public function destroy($id)
    {
        $eventType = EventType::findOrFail($id);
        $eventType->delete();
        return redirect()->route('admin.event-types.index')->with('success', 'EventType deleted successfully.');
    }
}
