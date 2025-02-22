<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\EventType;
use App\Models\Event;
use App\Models\User;
use App\Models\Accommodation;
use Carbon\Carbon; 

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Buat event type
        $workshop = EventType::create(['name' => 'Workshop']);
        $symposium = EventType::create(['name' => 'Symposium Offline']);

        // Tambahkan data Workshop
        Event::create(['event_type_id' => $workshop->id, 'name' => 'Workshop Prostat Enucleation', 'early_bid_price' => 7500000, 'onsite_price' =>7500000, 'early_bid_date' => Carbon::parse('2025-03-31'), 'event_date' => Carbon::parse('2025-05-08'), 'kuota' => 10]);
        // Event::create(['event_type_id' => $workshop->id, 'name' => 'Observer Workshop Prostat Enukleasi', 'early_bid_price' => 2750000, 'onsite_price' =>2750000, 'early_bid_date' => Carbon::parse('2025-03-31'), 'event_date' => Carbon::parse('2025-05-08'), 'kuota' => 10]);
        Event::create(['event_type_id' => $workshop->id, 'name' => 'Workshop Urogenital Recontruction', 'early_bid_price' => 7500000, 'onsite_price' =>7500000, 'early_bid_date' => Carbon::parse('2025-03-31'), 'event_date' => Carbon::parse('2025-05-09'), 'kuota' => 10]);
        // Event::create(['event_type_id' => $workshop->id, 'name' => 'Observer Workshop InaGURS', 'early_bid_price' => 2750000, 'onsite_price' =>2750000, 'early_bid_date' => Carbon::parse('2025-03-31'), 'event_date' => Carbon::parse('2025-05-09'), 'kuota' => 10]);

        // Tambahkan data Symposium Offline
        Event::create(['event_type_id' => $symposium->id, 'name' => 'Student', 'early_bid_price' => 750000,  'onsite_price' => 1000000, 'early_bid_date' => Carbon::parse('2025-03-31'), 'event_date' => Carbon::parse('2025-05-10'), 'event_date_day2' => Carbon::parse('2025-05-11'), 'kuota' => 100]);
        Event::create(['event_type_id' => $symposium->id, 'name' => 'General Practitioner/Resident', 'early_bid_price' => 1750000, 'onsite_price' => 2000000, 'early_bid_date' => Carbon::parse('2025-03-31'), 'event_date' => Carbon::parse('2025-05-09'), 'event_date_day2' => Carbon::parse('2025-05-10'), 'kuota' => 100]);
        Event::create(['event_type_id' => $symposium->id, 'name' => 'Specialist', 'early_bid_price' => 4000000, 'onsite_price' => 4500000, 'early_bid_date' => Carbon::parse('2025-03-31'), 'event_date' => Carbon::parse('2025-05-10'), 'event_date_day2' => Carbon::parse('2025-05-11'), 'kuota' => 100]); 
        
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'dikkytaopik22@gmail.com',
            'password' => Hash::make('Tanjungsari_13'),
            'is_admin' => true,
        ]);

        Accommodation::create([
            'name' => 'Room Type Deluxe',
            'price' => 2000000.00,
            'qty' => 5, 
            'location' => 'Bandung',
            'description' => 'Kamar deluxe dengan fasilitas mewah untuk kenyamanan Anda.',
        ]);
        
    }
}
