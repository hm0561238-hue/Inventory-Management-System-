<?php

namespace App\Services;

use App\Models\BarcodeScan;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;

class BarcodeService
{
    public function __construct(
        protected ProductRepository $products,
    ) {
    }

    /**
     * Search for a product by barcode
     */
    public function searchByBarcode(string $barcode, int $userId): array
    {
        try {
            $product = Product::where('barcode', $barcode)
                ->orWhere('sku', $barcode)
                ->first();

            if ($product) {
                $this->logScan($userId, $barcode, $product->id, 'success');

                return [
                    'success' => true,
                    'product' => $product,
                    'message' => 'Product found',
                ];
            }

            $this->logScan($userId, $barcode, null, 'not_found', 'No product found with this barcode');

            return [
                'success' => false,
                'product' => null,
                'message' => 'No product found with barcode: ' . $barcode,
            ];
        } catch (\Exception $e) {
            $this->logScan($userId, $barcode, null, 'error', $e->getMessage());

            return [
                'success' => false,
                'product' => null,
                'message' => 'Error processing barcode: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Log barcode scan for audit trail
     */
    protected function logScan(int $userId, string $barcode, ?int $productId, string $status, ?string $errorMessage = null): void
    {
        BarcodeScan::create([
            'user_id' => $userId,
            'barcode' => $barcode,
            'product_id' => $productId,
            'status' => $status,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Get recent barcode scans
     */
    public function getRecentScans(int $userId, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return BarcodeScan::where('user_id', $userId)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get barcode scan statistics
     */
    public function getScanStatistics(?int $userId = null, int $days = 7): array
    {
        $query = BarcodeScan::whereBetween('created_at', [now()->subDays($days), now()]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $baseData = $query->get();

        return [
            'total_scans' => $baseData->count(),
            'successful_scans' => $baseData->where('status', 'success')->count(),
            'failed_scans' => $baseData->where('status', 'not_found')->count(),
            'error_scans' => $baseData->where('status', 'error')->count(),
            'success_rate' => $baseData->count() > 0
                ? round(($baseData->where('status', 'success')->count() / $baseData->count()) * 100, 2)
                : 0,
        ];
    }
}
