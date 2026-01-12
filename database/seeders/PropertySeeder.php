<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cleanup old storage to prevent duplicate images piling up
        Storage::disk('public')->deleteDirectory('properties');
        
        // 2. Define Source Path
        $sourcePath = database_path('seeders/images');
        
        // Check if images exist
        if (!File::exists($sourcePath . '/house.jpg')) {
            $this->command->error('STOP! Please put house.jpg, warehouse.jpg, and apartment.jpg in database/seeders/images/ folder first.');
            return;
        }

        // 3. Create Agents
        $agent1 = User::firstOrCreate(['email' => 'agent.budi@rumahimpian.com'], [
            'name' => 'Budi Santoso',
            'password' => Hash::make('password'),
            'role' => 'AGENT',
            'phone_number' => '081234567890'
        ]);

        $agent2 = User::firstOrCreate(['email' => 'agent.siti@rumahimpian.com'], [
            'name' => 'Siti Aminah',
            'password' => Hash::make('password'),
            'role' => 'AGENT',
            'phone_number' => '089876543210'
        ]);

        // 4. THE DATASET (Grouped to trigger Recommendations)
        $properties = [
            // --- GROUP A: BANDUNG RESIDENTIAL (They should recommend each other) ---
            [
                'user_id' => $agent1->id,
                'title' => 'Luxury Villa Dago Pakar',
                'slug' => 'luxury-villa-dago-pakar',
                'price' => 5500000000, // 5.5 M
                'listing_type' => 'SALE',
                'category' => 'RESIDENTIAL',
                'property_type' => 'Villa',
                'city' => 'Bandung',
                'district' => 'Dago',
                'address' => 'Resor Dago Pakar, Cluster V',
                'latitude' => -6.8732, 'longitude' => 107.6258,
                'bedrooms' => 5, 'bathrooms' => 4, 'land_area' => 300, 'building_area' => 250,
                'specifications' => ['view' => 'City View', 'pool' => 'Private', 'wifi' => 'Included'],
                'status' => 'PUBLISHED',
                'image_source' => 'house.jpg' 
            ],
            [
                'user_id' => $agent1->id,
                'title' => 'Minimalist House Buah Batu',
                'slug' => 'minimalist-house-buah-batu',
                'price' => 1200000000, // 1.2 M
                'listing_type' => 'SALE',
                'category' => 'RESIDENTIAL',
                'property_type' => 'House',
                'city' => 'Bandung',
                'district' => 'Buah Batu',
                'address' => 'Jl. Buah Batu Regency',
                'latitude' => -6.9479, 'longitude' => 107.6365,
                'bedrooms' => 2, 'bathrooms' => 1, 'land_area' => 90, 'building_area' => 70,
                'specifications' => ['carport' => '1 Car', 'water' => 'PDAM', 'electricity' => '1300 W'],
                'status' => 'PUBLISHED',
                'image_source' => 'house.jpg'
            ],
            [
                'user_id' => $agent2->id,
                'title' => 'Apartment Ciumbuleuit Gallery',
                'slug' => 'apartment-ciumbuleuit',
                'price' => 850000000, // 850 Juta
                'listing_type' => 'SALE',
                'category' => 'RESIDENTIAL',
                'property_type' => 'Apartment',
                'city' => 'Bandung',
                'district' => 'Ciumbuleuit',
                'address' => 'Jl. Ciumbuleuit No. 42',
                'latitude' => -6.8778, 'longitude' => 107.6068,
                'bedrooms' => 1, 'bathrooms' => 1, 'land_area' => 0, 'building_area' => 36,
                'specifications' => ['floor' => '15', 'view' => 'Mountain', 'furnishing' => 'Full'],
                'status' => 'PUBLISHED',
                'image_source' => 'apartment.jpg'
            ],

            // --- GROUP B: JAKARTA COMMERCIAL (They should recommend each other) ---
            [
                'user_id' => $agent2->id,
                'title' => 'Premium Office SCBD',
                'slug' => 'premium-office-scbd',
                'price' => 45000000000, // 45 M
                'listing_type' => 'SALE',
                'category' => 'COMMERCIAL',
                'property_type' => 'Office',
                'city' => 'Jakarta Selatan',
                'district' => 'SCBD',
                'address' => 'District 8, Senopati',
                'latitude' => -6.2297, 'longitude' => 106.8085,
                'bedrooms' => 0, 'bathrooms' => 4, 'land_area' => 0, 'building_area' => 500,
                'specifications' => ['floor' => '30', 'parking' => 'Reserved', 'lift' => 'Private'],
                'status' => 'PUBLISHED',
                'image_source' => 'apartment.jpg' // Reusing apartment image for office
            ],
            [
                'user_id' => $agent1->id,
                'title' => 'Ruko Kelapa Gading 3 Floors',
                'slug' => 'ruko-kelapa-gading',
                'price' => 3500000000, // 3.5 M
                'listing_type' => 'SALE',
                'category' => 'COMMERCIAL',
                'property_type' => 'Ruko',
                'city' => 'Jakarta Utara',
                'district' => 'Kelapa Gading',
                'address' => 'Jl. Boulevard Raya',
                'latitude' => -6.1554, 'longitude' => 106.9112,
                'bedrooms' => 2, 'bathrooms' => 3, 'land_area' => 80, 'building_area' => 200,
                'specifications' => ['facing' => 'Main Road', 'electricity' => '4400 W'],
                'status' => 'PUBLISHED',
                'image_source' => 'warehouse.jpg' // Reusing warehouse image
            ],

            // --- GROUP C: BALI (Holiday Homes) ---
            [
                'user_id' => $agent2->id,
                'title' => 'Tropical Villa Canggu',
                'slug' => 'tropical-villa-canggu',
                'price' => 8000000000, // 8 M
                'listing_type' => 'SALE',
                'category' => 'RESIDENTIAL',
                'property_type' => 'Villa',
                'city' => 'Bali',
                'district' => 'Canggu',
                'address' => 'Jl. Pantai Batu Bolong',
                'latitude' => -8.6595, 'longitude' => 115.1301,
                'bedrooms' => 4, 'bathrooms' => 5, 'land_area' => 500, 'building_area' => 350,
                'specifications' => ['pool' => 'Infinity', 'style' => 'Modern Balinese', 'license' => 'Pondok Wisata'],
                'status' => 'PUBLISHED',
                'image_source' => 'house.jpg'
            ],
        ];

        // 5. Loop, Create, and Copy Image
        foreach ($properties as $data) {
            $imageFile = $data['image_source'];
            unset($data['image_source']);

            $prop = Property::firstOrCreate(['slug' => $data['slug']], $data);

            // Copy file to storage
            $targetFileName = 'properties/' . $prop->slug . '.jpg';
            Storage::disk('public')->put(
                $targetFileName, 
                file_get_contents($sourcePath . '/' . $imageFile)
            );

            // Create Media Record
            PropertyMedia::firstOrCreate([
                'property_id' => $prop->id,
                'file_path' => $targetFileName
            ], [
                'file_type' => 'IMAGE',
                'sort_order' => 0
            ]);
        }
        
        $this->command->info('Seeding Complete! Created 6 Properties.');
    }
}