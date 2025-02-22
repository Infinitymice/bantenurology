<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AccommodationController extends Controller
{
    public function index()
    {
        return view('admin.accommodation.index');
    }

    public function getData()
    {
        $accommodations = Accommodation::query();
        
        return DataTables::of($accommodations)
            ->addIndexColumn()
            ->addColumn('actions', function ($row) {
                return '
                    <div class="d-flex">
                        <a href="'.route('admin.accommodation.edit', $row->id).'" class="btn btn-sm btn-warning me-2">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'">Hapus</button>
                    </div>
                ';
            })
            ->editColumn('price', function ($row) {
                return 'Rp. ' . number_format($row->price, 0, ',', '.');
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>';
            })
            ->rawColumns(['actions', 'is_active'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        Accommodation::create($validated);
        
        return redirect()
            ->route('admin.accommodation.index')
            ->with('success', 'Akomodasi berhasil ditambahkan');
    }
    

    public function create()
    {
        return view('admin.accommodation.create');
    }

    public function edit(Accommodation $accommodation)
    {
        return view('admin.accommodation.edit', compact('accommodation'));
    }

    public function update(Request $request, Accommodation $accommodation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $accommodation->update($validated);
        return response()->json(['success' => true]);
    }

    public function destroy(Accommodation $accommodation)
    {
        $accommodation->delete();
        return response()->json(['success' => true]);
    }
}