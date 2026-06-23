<?php

namespace App\Console\Commands;

use App\Services\Ecommerce\SteadfastService;
use Illuminate\Console\Command;

/**
 * Polls Steadfast for the latest delivery status of every order with an open
 * (non-terminal) consignment and persists it. Intended to be run from the admin
 * task scheduler (add a task with command `steadfast:sync`).
 */
class SyncSteadfastStatuses extends Command
{
    protected $signature = 'steadfast:sync {--limit=100 : Max number of open consignments to sync per run}';

    protected $description = 'Refresh delivery status for open Steadfast consignments.';

    public function handle(): int
    {
        if (! SteadfastService::isEnabled()) {
            $this->info('Steadfast is not enabled — nothing to sync.');

            return self::SUCCESS;
        }

        $synced = SteadfastService::syncOpenConsignments((int) $this->option('limit'));

        $this->info("Synced {$synced} Steadfast consignment(s).");

        return self::SUCCESS;
    }
}
