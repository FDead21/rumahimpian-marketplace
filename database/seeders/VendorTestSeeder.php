<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. THE MEGA VENDOR (To test the Modal Scrollbar)
        // Let's give them 12 specific menu items.
        $massiveMenu = [];
        for ($i = 1; $i <= 12; $i++) {
            $massiveMenu[] = [
                'item_name'   => "Premium Stall Level {$i}",
                'price'       => 1500000 + ($i * 100000),
                'description' => "Delicious catering stall option number {$i} with premium ingredients.",
                'image'       => null, 
            ];
        }

        Vendor::create([
            'name'                 => 'Grand Rasa Catering (MEGA MENU)',
            'category'             => 'Catering',
            'phone'                => '081234567890',
            'city'                 => 'Bandung',
            'description'          => 'The biggest catering vendor to test your modal scrolling limits.',
            'detailed_description' => '<p>This vendor has a ton of specific menu items to ensure the modal scrollbar works perfectly without breaking the screen height.</p>',
            'features'             => ['Halal', 'Buffet', 'Stalls', 'Free Testing'],
            'service_menu'         => $massiveMenu,
            'price_from'           => 15000000,
            'is_active'            => true,
        ]);

        // 2. THE BULK VENDORS (To test the Accordion Scrollbar)
        // Let's create 8 Photography vendors so the Photography accordion gets really long.
        for ($i = 1; $i <= 8; $i++) {
            Vendor::create([
                'name'         => "Lensa Magic Photography - Team {$i}",
                'category'     => 'Photography',
                'phone'        => '089988776655',
                'city'         => 'Bandung',
                'description'  => "Standard photography team {$i}. No specific menu, just a base price.",
                'features'     => ['Drone', 'Album', 'Cinematic'],
                'service_menu' => null, // No menu = shows up as standard checkbox!
                'price_from'   => 3500000 + ($i * 500000),
                'is_active'    => true,
            ]);
        }

        // 3. A MIXED VENDOR (To test both)
        Vendor::create([
            'name'         => 'Acoustic Soul Entertainment',
            'category'     => 'Entertainment',
            'phone'        => '087711223344',
            'city'         => 'Jakarta',
            'description'  => 'Flexible band with a few add-on options.',
            'features'     => ['Acoustic', 'Full Band', 'MC Included'],
            'price_from'   => 4000000,
            'is_active'    => true,
            'service_menu' => [
                ['item_name' => 'Add Grand Piano', 'price' => 2000000, 'description' => 'Bring a real grand piano to the venue.'],
                ['item_name' => 'Extra 1 Hour', 'price' => 1000000, 'description' => 'Keep the party going.'],
                ['item_name' => 'Female Singer Add-on', 'price' => 1500000, 'description' => 'Duet performance.'],
            ]
        ]);
        
        // 4. EXTRA CATEGORY FILLERS
        $categories = ['Decoration', 'Florist', 'Makeup', 'Transport'];
        foreach ($categories as $cat) {
            Vendor::create([
                'name'         => "Beautiful {$cat} Vendor",
                'category'     => $cat,
                'city'         => 'Bandung',
                'description'  => "A reliable {$cat} vendor.",
                'price_from'   => 2500000,
                'is_active'    => true,
            ]);
        }
    }
}