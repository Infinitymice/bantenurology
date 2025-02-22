<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupCode;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class GroupCodeController extends Controller
{
    public function index()
    {
        return view('admin.group.group-codes');
    }

    public function generate()
    {
        $code = 'GRP-' . strtoupper(uniqid());
        $groupCode = GroupCode::create([
            'code' => $code,
            'max_members' => 6,
            'current_members' => 0,
            'is_active' => true
        ]);

        return response()->json([
            'success' => true,
            'code' => $code
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'max_members' => 'required|integer|min:1',
            'code' => 'required|string|unique:group_codes,code'
        ]);
    
        GroupCode::create([
            'code' => $request->code,
            'group_name' => $request->group_name,
            'max_members' => $request->max_members,
            'current_members' => 0,
            'is_active' => true
        ]);
    
        return redirect()->route('admin.group-codes.index')
            ->with('success', 'Grup berhasil ditambahkan');
    }
    
    public function getData()
    {
        $groupCodes = GroupCode::select(['id', 'code', 'group_name', 'current_members', 'max_members', 'is_active']);
        
        return DataTables::of($groupCodes)
            ->addColumn('is_active', function($groupCode) {
                return $groupCode->is_active ? 
                    '<span class="badge badge-success">Aktif</span>' : 
                    '<span class="badge badge-danger">Tidak Aktif</span>';
            })
            ->addColumn('action', function($groupCode) {
                $buttons = '<button class="btn btn-sm btn-warning" onclick="editGroup('.$groupCode->id.')">Edit</button> ';
                $buttons .= '<button class="btn btn-sm btn-'.($groupCode->is_active ? 'danger' : 'success').'" onclick="toggleStatus('.$groupCode->id.')">'
                        .($groupCode->is_active ? 'Nonaktifkan' : 'Aktifkan').'</button>';
                return $buttons;
            })
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function edit($id)
    {
        $groupCode = GroupCode::findOrFail($id);
        return response()->json($groupCode);
    }

    public function update(Request $request, $id)
    {
        $groupCode = GroupCode::findOrFail($id);
        
        $request->validate([
            'group_name' => 'required|string|max:255',
            'max_members' => 'required|integer|min:' . $groupCode->current_members,
            'code' => 'required|string|unique:group_codes,code,' . $id
        ]);

        $groupCode->update([
            'group_name' => $request->group_name,
            'max_members' => $request->max_members,
            'code' => $request->code
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Grup berhasil diperbarui'
        ]);
    }

    public function toggleStatus($id)
    {
        $groupCode = GroupCode::findOrFail($id);
        $groupCode->is_active = !$groupCode->is_active;
        $groupCode->save();

        return response()->json([
            'success' => true,
            'message' => 'Status grup berhasil diubah'
        ]);
    }

    public function destroy($id)
    {
        $groupCode = GroupCode::findOrFail($id);
        
        // Check if group has members
        if ($groupCode->current_members > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus grup yang masih memiliki anggota'
            ], 422);
        }

        $groupCode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Grup berhasil dihapus'
        ]);
    }
}
