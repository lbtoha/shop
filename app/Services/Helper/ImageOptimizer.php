<?php

namespace App\Services\Helper;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class ImageOptimizer extends Command
{
    public static function optimize($file)
    {
        try {
            $imageManager = new ImageManager(new Driver());

            $tmpPath = $file->getPathname();
            $extension = strtolower($file->getClientOriginalExtension());

            if (! in_array($extension, ['jpg', 'jpeg', 'png'])) {
                return $file;
            }

            // Create WebP temp file
            $webpPath = $tmpPath . '.webp';

            $image = $imageManager->read($tmpPath);

            // Resize
            if ($image->width() > 1024) {
                $image->scale(width: 1024);
            }

            $image->toWebp(quality: 70)->save($webpPath);

            OptimizerChainFactory::create()->optimize($webpPath);

            return new \Illuminate\Http\UploadedFile(
                $webpPath,
                pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp',
                'image/webp',
                null,
                true
            );

        } catch (\Throwable $e) {
            report($e);
            return $file;
        }
    }

}
