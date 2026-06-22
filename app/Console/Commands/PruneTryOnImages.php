<?php

namespace App\Console\Commands;

use App\Services\Ai\GeminiTryOnService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Deletes generated virtual try-on images older than the configured TTL, so
 * AI-generated previews of customers don't linger on disk.
 */
class PruneTryOnImages extends Command
{
    protected $signature = 'tryon:prune {--hours=24 : Delete results older than this many hours}';

    protected $description = 'Delete AI virtual try-on result images older than the TTL.';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $dir = GeminiTryOnService::RESULT_DIR;

        if (! $disk->exists($dir)) {
            $this->info('No try-on directory yet — nothing to prune.');

            return self::SUCCESS;
        }

        $cutoff = now()->subHours((int) $this->option('hours'))->getTimestamp();
        $deleted = 0;

        foreach ($disk->files($dir) as $file) {
            if ($disk->lastModified($file) < $cutoff) {
                $disk->delete($file);
                $deleted++;
            }
        }

        $this->info("Pruned {$deleted} try-on image(s).");

        return self::SUCCESS;
    }
}
