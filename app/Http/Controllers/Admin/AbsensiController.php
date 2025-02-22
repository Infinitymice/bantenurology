<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AbsensiController extends Controller
{
    public function index()
    {
        return view('admin.absensi.index'); 
    }

    // data absensi 

    // public function getData(Request $request)
    // {
    //     \Log::info('Request Parameters:', $request->all());

    //     // Mulai query untuk mengambil absensi dengan join ke tabel registrasi
    //     $query = Absensi::join('registrasis', 'absensi.registrasi_id', '=', 'registrasis.id')
    //         ->select('absensi.*', 'registrasis.full_name', 'registrasis.email', 'absensi.session');

    //     // Filter berdasarkan tanggal jika ada
    //     if ($request->has('date') && !empty($request->date)) {
    //         $selectedDate = Carbon::createFromFormat('Y-m-d', $request->date)->startOfDay();
    //         $query->whereDate('absensi.waktu_absen', '=', $selectedDate->toDateString());
    //     }

    //     // Filter berdasarkan sesi jika ada
    //     if ($request->has('sesi') && !empty($request->sesi)) {
    //         $query->where('absensi.session', '=', $request->sesi);
    //     }

    //     // Filter berdasarkan pencarian (nama, email, status absensi)
    //     if ($request->has('search') && isset($request->search['value']) && !is_bool($request->search['value'])) {
    //         $search = $request->search['value'];
    //         $query->where(function($q) use ($search) {
    //             $q->where('registrasis.full_name', 'like', "%$search%")
    //                 ->orWhere('registrasis.email', 'like', "%$search%")
    //                 ->orWhere('absensi.status_absen', 'like', "%$search%");
    //         });
    //     }

    //     // Sorting
    //     $columns = $request->get('order');
    //     if (isset($columns[0]['column'])) {
    //         $column = $columns[0]['column']; 
    //         $dir = $columns[0]['dir']; 
    //         $orderBy = $request->columns[$column]['name']; // Nama kolom untuk di-sort
            
    //         // Hanya lakukan sorting pada kolom yang ada dalam database
    //         if (in_array($orderBy, ['full_name', 'email', 'status_absen', 'waktu_absen', 'session'])) {
    //             $query->orderBy($orderBy, $dir);
    //         }
    //     }

    //     // Pagination menggunakan paginate()
    //     $length = $request->get('length', 10); // Jumlah data per halaman
    //     $start = $request->get('start', 0); 
    //     $absensi = $query->skip($start)->take($length)->get(); // Ambil data yang difilter dan dipaging

    //     // Hitung jumlah total data tanpa filter
    //     $recordsTotal = Absensi::count(); // Jumlah total tanpa filter

    //     // Hitung jumlah data yang sesuai dengan filter
    //     $recordsFiltered = $query->count(); // Jumlah data setelah filter

    //     // Jika ekspor semua data
    //     if ($request->has('export_all') && $request->export_all) {
    //         $data = $absensi->map(function ($item, $index) {
    //             return [
    //                 'No' => $index + 1, // Nomor urut
    //                 'Nama Peserta' => $item->full_name ?? 'N/A',
    //                 'Email' => $item->email ?? 'N/A',
    //                 'Status Absen' => $item->status_absen,
    //                 'Waktu Absen' => $item->waktu_absen->format('Y-m-d H:i:s'),
    //                 'Session' => $item->session ?? 'N/A'
    //             ];
    //         });

    //         return response()->json($data);
    //     }

    //     // Format data untuk DataTables
    //     $data = $absensi->map(function ($item, $index) use ($start) {
    //         return [
    //             'DT_RowIndex' => $start + $index + 1,  
    //             'full_name' => $item->full_name ?? 'N/A',
    //             'email' => $item->email ?? 'N/A',
    //             'status_absen' => $item->status_absen,
    //             'waktu_absen' => $item->waktu_absen->format('Y-m-d H:i:s'),
    //             'session' => $item->session ?? 'N/A'
    //         ];
    //     });

    //     return response()->json([
    //         'draw' => $request->get('draw'),
    //         'recordsTotal' => $recordsTotal, // Total records tanpa filter
    //         'recordsFiltered' => $recordsFiltered, // Total records setelah filter
    //         'data' => $data // Mengembalikan data yang telah diformat
    //     ]);
    // }

    public function getData(Request $request)
    {
        \Log::info('Request Parameters:', $request->all());

        // Mulai query untuk mengambil absensi dengan join ke tabel registrasi
        $query = Absensi::join('registrasis', 'absensi.registrasi_id', '=', 'registrasis.id')
            ->select('absensi.*', 'registrasis.full_name', 'registrasis.email', 'absensi.session');

        // Filter berdasarkan tanggal jika ada
        if ($request->has('date') && !empty($request->date)) {
            $selectedDate = Carbon::createFromFormat('Y-m-d', $request->date)->startOfDay();
            $query->whereDate('absensi.waktu_absen', '=', $selectedDate->toDateString());
        }

        // Filter berdasarkan sesi jika ada
        if ($request->has('sesi') && !empty($request->sesi)) {
            $query->where('absensi.session', '=', $request->sesi);
        }

        // Filter berdasarkan pencarian (nama, email, status absensi)
        if ($request->has('search') && isset($request->search['value']) && !is_bool($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('registrasis.full_name', 'like', "%$search%")
                    ->orWhere('registrasis.email', 'like', "%$search%")
                    ->orWhere('absensi.status_absen', 'like', "%$search%");
            });
        }

        // Sorting
        $columns = $request->get('order');
        if (isset($columns[0]['column'])) {
            $column = $columns[0]['column']; 
            $dir = $columns[0]['dir']; 
            $orderBy = $request->columns[$column]['name']; // Nama kolom untuk di-sort
            
            // Hanya lakukan sorting pada kolom yang ada dalam database
            if (in_array($orderBy, ['full_name', 'email', 'status_absen', 'waktu_absen', 'session'])) {
                $query->orderBy($orderBy, $dir);
            }
        }

        // Pagination menggunakan paginate()
        $length = $request->get('length', 10); // Jumlah data per halaman
        $start = $request->get('start', 0);

        // Menggunakan paginate untuk memastikan pagination bekerja dengan benar
        $absensi = $query->paginate($length);

        // Hitung jumlah total data tanpa filter
        $recordsTotal = Absensi::count(); // Jumlah total tanpa filter

        // Hitung jumlah data yang sesuai dengan filter
        $recordsFiltered = $query->count(); // Jumlah data setelah filter

        // Jika ekspor semua data
        if ($request->has('export_all') && $request->export_all) {
            // Ambil seluruh data tanpa pagination
            $data = $query->get();

            // Siapkan data ekspor
            $exportData = $data->map(function ($item, $index) {
                return [
                    'No' => $index + 1, // Nomor urut
                    'Nama Peserta' => $item->full_name ?? 'N/A',
                    'Email' => $item->email ?? 'N/A',
                    'Status Absen' => $item->status_absen,
                    'Waktu Absen' => $item->waktu_absen->format('Y-m-d H:i:s'),
                    'Session' => $item->session ?? 'N/A'
                ];
            });

            // Kembalikan data ekspor sebagai JSON
            return response()->json($exportData);
        }

        // Format data untuk DataTables
        $data = $absensi->map(function ($item, $index) use ($start) {
            return [
                'DT_RowIndex' => $start + $index + 1,  
                'full_name' => $item->full_name ?? 'N/A',
                'email' => $item->email ?? 'N/A',
                'status_absen' => $item->status_absen,
                'waktu_absen' => $item->waktu_absen->format('Y-m-d H:i:s'),
                'session' => $item->session ?? 'N/A'
            ];
        });

        // Mengembalikan response dengan data yang sesuai
        return response()->json([
            'draw' => $request->get('draw'),
            'recordsTotal' => $recordsTotal, // Jumlah total data tanpa filter
            'recordsFiltered' => $recordsFiltered, // Jumlah data setelah filter
            'data' => $data // Mengembalikan data yang telah diformat
        ]);
    }




    public function deleteAll(Request $request)
    {
        try {
            // Hapus semua data absensi
            DB::table('absensi')->delete();

            return response()->json(['success' => 'Semua data absensi berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus data'], 500);
        }
    }

    public function deleteByDate(Request $request)
    {
        try {
            $date = $request->input('date');
            Absensi::whereDate('waktu_absen', $date)->delete();
            
            return response()->json([
                'success' => 'Data absensi untuk tanggal ' . $date . ' berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menghapus data absensi'
            ], 500);
        }
    }


    // public function resetAbsensi(Request $request)
    // {
    //     // Set session untuk tanggal reset absensi hari ini
    //     session(['last_reset_date' => now()->format('Y-m-d')]);

    //     return response()->json(['success' => true]);
    // }

        
}
