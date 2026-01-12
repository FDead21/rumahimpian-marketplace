<?php

namespace App\Observers;

use App\Models\PropertyMedia;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PropertyMediaObserver
{
    public function created(PropertyMedia $media): void
    {
        if ($media->file_type !== 'IMAGE') {
            return;
        }

        $path = storage_path('app/public/' . $media->file_path);

        if (file_exists($path)) {
            $manager = new ImageManager(new Driver());

            $image = $manager->read($path);

            // Add Watermark Text
            $image->text('RumahImpian', 50, 50, function ($font) {
                $font->size(48);
                $font->color('rgba(255, 255, 255, 0.5)'); 
                $font->align('left');
                $font->valign('top');
            });

            // Save it back (Overwrite original)
            $image->save($path);
        }
    }
}