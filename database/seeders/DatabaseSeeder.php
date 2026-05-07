<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@hotel.com',
            'password' => Hash::make('12345678'),
            'role'     => 'admin',
            'status'   => 'active',
            'phone'    => '+1 (555) 000-0001',
            'address'  => 'Hotel Headquarters',
        ]);

        // Create sample staff
        User::create([
            'name'     => 'John Staff',
            'email'    => 'staff@hotel.com',
            'password' => Hash::make('12345678'),
            'role'     => 'staff',
            'status'   => 'active',
            'phone'    => '+1 (555) 000-0002',
        ]);

        // Create sample customer
        User::create([
            'name'     => 'Jane Customer',
            'email'    => 'customer@hotel.com',
            'password' => Hash::make('12345678'),
            'role'     => 'customer',
            'status'   => 'active',
            'phone'    => '+1 (555) 000-0003',
        ]);

        // Create sample rooms
        $rooms = [
            [
                'room_number'    => '101',
                'room_type'      => 'Standard',
                'description'    => 'Comfortable standard room with queen bed, flat-screen TV, and private bathroom.',
                'price_per_night'=> 89.00,
                'capacity'       => 2,
                'status'         => 'available',
                'floor'          => 1,
                'amenities'      => json_encode(['WiFi', 'TV', 'AC', 'Mini Bar']),
            ],
            [
                'room_number'    => '102',
                'room_type'      => 'Standard',
                'description'    => 'Comfortable standard room with twin beds and city view.',
                'price_per_night'=> 79.00,
                'capacity'       => 2,
                'status'         => 'available',
                'floor'          => 1,
                'amenities'      => json_encode(['WiFi', 'TV', 'AC']),
            ],
            [
                'room_number'    => '201',
                'room_type'      => 'Deluxe',
                'description'    => 'Spacious deluxe room with king bed, sitting area, and premium amenities.',
                'price_per_night'=> 149.00,
                'capacity'       => 3,
                'status'         => 'available',
                'floor'          => 2,
                'amenities'      => json_encode(['WiFi', 'TV', 'AC', 'Mini Bar', 'Jacuzzi', 'Room Service']),
            ],
            [
                'room_number'    => '202',
                'room_type'      => 'Deluxe',
                'description'    => 'Elegant deluxe room with ocean view and balcony.',
                'price_per_night'=> 169.00,
                'capacity'       => 2,
                'status'         => 'available',
                'floor'          => 2,
                'amenities'      => json_encode(['WiFi', 'TV', 'AC', 'Mini Bar', 'Balcony', 'Room Service']),
            ],
            [
                'room_number'    => '301',
                'room_type'      => 'Suite',
                'description'    => 'Luxurious suite with separate living room, king bed, and panoramic views.',
                'price_per_night'=> 299.00,
                'capacity'       => 4,
                'status'         => 'available',
                'floor'          => 3,
                'amenities'      => json_encode(['WiFi', 'TV', 'AC', 'Mini Bar', 'Jacuzzi', 'Room Service', 'Kitchen', 'Living Room']),
            ],
            [
                'room_number'    => '302',
                'room_type'      => 'Suite',
                'description'    => 'Presidential suite with butler service and exclusive amenities.',
                'price_per_night'=> 499.00,
                'capacity'       => 6,
                'status'         => 'available',
                'floor'          => 3,
                'amenities'      => json_encode(['WiFi', 'TV', 'AC', 'Mini Bar', 'Jacuzzi', 'Room Service', 'Kitchen', 'Living Room', 'Butler Service']),
            ],
            [
                'room_number'    => '401',
                'room_type'      => 'Family',
                'description'    => 'Spacious family room with two bedrooms and family-friendly amenities.',
                'price_per_night'=> 199.00,
                'capacity'       => 5,
                'status'         => 'available',
                'floor'          => 4,
                'amenities'      => json_encode(['WiFi', 'TV', 'AC', 'Mini Bar', 'Extra Beds', 'Room Service']),
            ],
            [
                'room_number'    => '402',
                'room_type'      => 'Family',
                'description'    => 'Comfortable family room perfect for families with children.',
                'price_per_night'=> 189.00,
                'capacity'       => 4,
                'status'         => 'maintenance',
                'floor'          => 4,
                'amenities'      => json_encode(['WiFi', 'TV', 'AC', 'Extra Beds']),
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
