<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\OrderExport;
use App\Data\OrderData;
use Exception;
use Throwable;
use Log;

class ExportOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $orderExportId
    )
    {
    }

    /**
     * Execute the job.
     * @throws Exception|Throwable
     */
    public function handle(): void
    {
        $orderExport = OrderExport::find($this->orderExportId);

        if (!$orderExport) {
            Log::error("OrderExport with ID {$this->orderExportId} not found.");
            return;
        }

        $orderExport->update(['status' => 'processing']);

        try {
            $order = Order::with(['customer', 'orderItems.product'])->find($orderExport->order_id);

            if (!$order) {
                throw new Exception("Order with ID {$orderExport->order_id} not found for export.");
            }

            $orderData = OrderData::fromModel($order);

            $externalApiUrl = config('services.external_order_export.url', 'https://httpbin.org/post');

            $response = Http::timeout(10)->post($externalApiUrl, $orderData->toArray());

            if ($response->failed()) {
                throw new Exception("Failed to export order ID {$orderExport->order_id}. Status: {$response->status()}, Response: {$response->body()}");
            }

            // Simulate file path for now
            $filePath = 'exports/order-' . $orderExport->order_id . '-' . time() . '.json';

            $orderExport->update([
                'status' => 'completed',
                'file_path' => $filePath,
            ]);

            Log::info("Order ID {$orderExport->order_id} successfully exported. Export ID: {$this->orderExportId}.");

        } catch (Throwable $e) {
            $orderExport->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error("Error exporting order ID {$orderExport->order_id} (Export ID: {$this->orderExportId}): " . $e->getMessage());
            throw $e;
        }
    }
}
