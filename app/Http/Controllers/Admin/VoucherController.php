<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function index()
    {
        $eventTypes = \App\Models\EventType::all();
        return view('admin.voucherdiskon.index', compact('eventTypes'));
    }
    public function show(Voucher $voucher)
    {
        // Ubah format tanggal ke UTC untuk input type="date"
        $voucher->valid_until = $voucher->valid_until ? Carbon::parse($voucher->valid_until)->format('Y-m-d') : null;
        return response()->json($voucher);
    }

    public function getData()
    {
        try {
            $vouchers = Voucher::query();

            return DataTables::of($vouchers)
                ->addColumn('status', function ($voucher) {
                    return $voucher->is_active ? 'Active' : 'Inactive';
                })
                ->addColumn('valid_until', function ($voucher) {
                    return $voucher->valid_until ? date('Y-m-d', strtotime($voucher->valid_until)) : 'No Expiry';
                })
                ->addColumn('discount_display', function ($voucher) {
                    try {
                        if (!$voucher->event_discounts || !$voucher->discount_types) return '-';

                        // Cek apakah data sudah dalam bentuk array
                        $discounts = is_array($voucher->event_discounts)
                            ? $voucher->event_discounts
                            : json_decode($voucher->event_discounts, true);

                        $types = is_array($voucher->discount_types)
                            ? $voucher->discount_types
                            : json_decode($voucher->discount_types, true);

                        if (!$discounts || !$types) return '-';

                        $eventTypes = \App\Models\EventType::pluck('name', 'id');
                        $displayValues = [];

                        foreach ($discounts as $typeId => $value) {
                            if (isset($eventTypes[$typeId])) {
                                $type = $types[$typeId] ?? 'percentage';
                                if ($type === 'percentage') {
                                    $displayValues[] = $eventTypes[$typeId] . ': ' . number_format($value, 2) . '%';
                                } else {
                                    $displayValues[] = $eventTypes[$typeId] . ': Rp ' . number_format($value, 0, ',', '.');
                                }
                            }
                        }

                        return empty($displayValues) ? '-' : implode('<br>', $displayValues);
                    } catch (\Exception $e) {
                        \Log::error('Error in discount_display: ' . $e->getMessage());
                        return '-';
                    }
                })
                ->addColumn('action', function ($voucher) {
                    return '
                        <button class="btn btn-sm btn-info edit-voucher" data-id="' . $voucher->id . '">Edit</button>
                        <button class="btn btn-sm btn-danger delete-voucher" data-id="' . $voucher->id . '">Delete</button>
                    ';
                })
                ->rawColumns(['action', 'discount_display'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code',
            'event_discounts' => 'required|array',
            'discount_types' => 'required|array',
            'discount_types.*' => 'nullable|in:percentage,fixed',
            'max_uses' => 'nullable|integer|min:1',
            'valid_until' => 'nullable|date|after:today',
        ]);

        // Validasi custom untuk setiap diskon berdasarkan tipenya
        foreach ($request->event_discounts as $typeId => $value) {
            if ($value !== null && $value !== '') {
                $discountType = $request->discount_types[$typeId] ?? 'percentage';

                if ($discountType === 'percentage' && $value > 100) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'event_discounts.' . $typeId => ['Percentage discount cannot be greater than 100%']
                        ]
                    ], 422);
                }

                if ($discountType === 'fixed' && $value <= 0) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'event_discounts.' . $typeId => ['Fixed discount must be greater than 0']
                        ]
                    ], 422);
                }
            }
        }

        // Filter dan simpan data diskon dan tipenya
        $eventDiscounts = [];
        $discountTypes = [];
        foreach ($request->event_discounts as $typeId => $value) {
            if ($value !== null && $value !== '') {
                $eventDiscounts[$typeId] = $value;
                $discountTypes[$typeId] = $request->discount_types[$typeId] ?? 'percentage';
            }
        }

        $validated['event_discounts'] = json_encode($eventDiscounts);
        $validated['discount_types'] = json_encode($discountTypes);
        $validated['is_active'] = $request->has('is_active');

        Voucher::create($validated);
        return response()->json(['success' => true]);
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code',
            'event_discounts' => 'required|array',
            'discount_types' => 'required|array',
            'discount_types.*' => 'nullable|in:percentage,fixed',
            'max_uses' => 'nullable|integer|min:1',
            'valid_until' => 'nullable|date|after:today',
        ]);

        // Validasi custom untuk setiap diskon berdasarkan tipenya
        foreach ($request->event_discounts as $typeId => $value) {
            if ($value !== null && $value !== '') {
                $discountType = $request->discount_types[$typeId] ?? 'percentage';

                if ($discountType === 'percentage' && $value > 100) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'event_discounts.' . $typeId => ['Percentage discount cannot be greater than 100%']
                        ]
                    ], 422);
                }

                if ($discountType === 'fixed' && $value <= 0) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'event_discounts.' . $typeId => ['Fixed discount must be greater than 0']
                        ]
                    ], 422);
                }
            }
        }

        // Filter dan simpan data diskon dan tipenya
        $eventDiscounts = [];
        $discountTypes = [];
        foreach ($request->event_discounts as $typeId => $value) {
            if ($value !== null && $value !== '') {
                $eventDiscounts[$typeId] = $value;
                $discountTypes[$typeId] = $request->discount_types[$typeId] ?? 'percentage';
            }
        }

        $voucher->update([
            'code' => $validated['code'],
            'event_discounts' => json_encode($eventDiscounts),
            'discount_types' => json_encode($discountTypes),
            'max_uses' => $validated['max_uses'],
            'valid_until' => $validated['valid_until'],
            'is_active' => $request->boolean('is_active')
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return response()->json(['success' => true]);
    }
}
