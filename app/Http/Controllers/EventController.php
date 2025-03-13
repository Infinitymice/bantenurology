<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventType;
use App\Models\Event;
use Carbon\Carbon;

class EventController extends Controller
{
    // public function getEventsByType($eventTypeId) {
    //     // Ambil events berdasarkan event type
    //     $events = Event::where('event_type_id', $eventTypeId)->get();
        
    //     return response()->json($events);
    // }

    public function getEventTypes()
    {
        $eventTypes = EventType::all(); 
        return response()->json($eventTypes);
    }

    public function getEventsByType($type)
    {
        try {
            $category = request('category');
            $voucherDiscounts = session('voucher_discounts', []);
            $voucherTypes = session('voucher_types', []);
            
            // Filter sesuai kategori
            if ($category === 'Student') {
                $events = Event::where('event_type_id', 2)->where('id', 3)->get();
            } elseif ($category === 'General Practitioner/Resident') {
                $events = Event::where('event_type_id', 2)->where('id', 4)->get();
            } elseif ($category === 'Specialist') {
                if ($type == '1') {
                    $events = Event::where('event_type_id', 1)->get();
                } elseif ($type == '2') {
                    $events = Event::where('event_type_id', 2)->where('id', 5)->get();
                }
            } else {
                return response()->json(['error' => 'Invalid category selected'], 400);
            }

            foreach ($events as $event) {
                $isEarlyBird = now() <= Carbon::parse($event->early_bid_date);
                $originalPrice = $isEarlyBird ? $event->early_bid_price : $event->onsite_price;
                
                $event->original_price = $originalPrice;
                $event->price = $originalPrice;
                $event->discount_percentage = 0;

                // Terapkan diskon jika ada
                if (isset($voucherDiscounts[$event->event_type_id])) {
                    $discountType = $voucherTypes[$event->event_type_id] ?? 'percentage';
                    $discountValue = $voucherDiscounts[$event->event_type_id];

                    if ($discountType === 'percentage') {
                        $discountAmount = ($originalPrice * $discountValue) / 100;
                        $event->price = max(0, $originalPrice - $discountAmount);
                        $event->discount_percentage = $discountValue;
                    } else {
                        $event->price = max(0, $originalPrice - $discountValue);
                        $event->discount_percentage = round(($discountValue / $originalPrice) * 100, 2);
                    }
                }

                $event->event_type_name = $event->eventType->name ?? 'N/A';
            }

            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }    
    
}
