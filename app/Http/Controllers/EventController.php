<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventType;
use App\Models\Event;

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
        $category = request('category'); // Ambil kategori dari permintaan
    
        // Filter sesuai kategori
        if ($category === 'Student') {
            $events = Event::where('event_type_id', 2)->where('id', 3)->get();
        } elseif ($category === 'General Practitioner/Resident') {
            $events = Event::where('event_type_id', 2)->where('id', 4)->get();
        } elseif ($category === 'Specialist') {
            if ($type == '1') { // Misalnya, ID 1 untuk Workshop
                $events = Event::where('event_type_id', 1)->get();
            } elseif ($type == '2') { // ID 2 untuk Symposium
                $events = Event::where('event_type_id', 2)->where('id', 5)->get();
            }
        } else {
            return response()->json(['error' => 'Invalid category selected'], 400);
        }
    
        return response()->json($events);
    }    
    
}
