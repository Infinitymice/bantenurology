<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventType;
use App\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buat event type
        $workshop = EventType::create(['name' => 'Workshop']);
        $symposium = EventType::create(['name' => 'Symposium Offline']);

        // Tambahkan data Workshop
        Event::create(['event_type_id' => $workshop->id, 'name' => 'Workshop Prostat Enuklasi', 'price' => 7500000]);
        Event::create(['event_type_id' => $workshop->id, 'name' => 'Workshop InUGURS', 'price' => 7500000]);
        Event::create(['event_type_id' => $workshop->id, 'name' => 'Observer WS Workshop Prostat Enuklasi', 'price' => 2750000]);
        Event::create(['event_type_id' => $workshop->id, 'name' => 'Observer WS Workshop InaGURS', 'price' => 2750000]);

        // Tambahkan data Symposium Offline
        Event::create(['event_type_id' => $symposium->id, 'name' => 'Student', 'price' => 750000]);
        Event::create(['event_type_id' => $symposium->id, 'name' => 'General Practitioner/Resident', 'price' => 1500000]);
        Event::create(['event_type_id' => $symposium->id, 'name' => 'Specialist', 'price' => 4000000]);   
    }
}
