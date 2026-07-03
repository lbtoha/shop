<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Tracking\FacebookCapiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFacebookCapiPurchaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Order $order,
        protected ?string $ip = null,
        protected ?string $userAgent = null,
        protected ?string $fbp = null,
        protected ?string $fbc = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        FacebookCapiService::sendPurchase(
            $this->order,
            $this->ip,
            $this->userAgent,
            $this->fbp,
            $this->fbc
        );
    }
}
