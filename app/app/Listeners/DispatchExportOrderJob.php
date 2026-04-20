<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Jobs\ExportOrderJob;
use App\Models\OrderExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DispatchExportOrderJob implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderConfirmed $event): void
    {
        $orderExport = OrderExport::create([
            'order_id' => $event->orderId,
            'status' => 'pending',
        ]);

        ExportOrderJob::dispatch($orderExport->id);
    }
}
