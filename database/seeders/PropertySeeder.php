<?php

namespace Database\Seeders;

use App\Models\Agency;
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
        // 1. Cleanup
        Storage::disk('public')->deleteDirectory('properties');
        Storage::disk('public')->deleteDirectory('agencies');
        
        $sourcePath = database_path('seeders/images');
        // Simple check to avoid crash if you haven't put images yet
        $hasImages = File::exists($sourcePath . '/house.jpg');

        // 2. CREATE MANUAL DATA (Your Demo Set)
        // ... (Keep the Agencies/Agents creation logic from previous code if you want specific users) ...
        
        // Let's create a "Random Generator" loop
        $this->command->info('🏭 Generating 50 Random Properties...');

        // Create 5 Random Agencies
        $randomAgencies = Agency::factory()->count(5)->create();

        // Create 10 Random Agents per Agency
        $randomAgents = User::factory()->count(10)->create([
            'role' => 'AGENT',
            'password' => Hash::make('password'),
            'agency_id' => $randomAgencies->random()->id
        ]);

        // Create 50 Properties distributed among these agents
        $properties = Property::factory()->count(50)->make()->each(function ($prop) use ($randomAgents, $hasImages, $sourcePath) {
            
            // Assign to a random agent
            $prop->user_id = $randomAgents->random()->id;
            $prop->save();

            // Attach a random image if available
            if ($hasImages) {
                $randomImage = collect(['house.jpg', 'apartment.jpg', 'warehouse.jpg'])->random();
                $targetName = 'properties/' . $prop->slug . '-' . uniqid() . '.jpg';
                
                Storage::disk('public')->put(
                    $targetName,
                    file_get_contents($sourcePath . '/' . $randomImage)
                );

                PropertyMedia::create([
                    'property_id' => $prop->id,
                    'file_path' => $targetName,
                    'file_type' => 'IMAGE',
                    'sort_order' => 0
                ]);
            }
        });

        $this->command->info('✅ Generated 50 Properties successfully!');
    }
}