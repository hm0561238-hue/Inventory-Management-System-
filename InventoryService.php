<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\StockMovementRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function __construct(
        protected StockMovementRepository $movements,
        protected DatabaseManager $db,
        protected StockAlertService $alertService,
    ) {
    }

    public function adjustStock(Product $product, int $quantity, string $type, int $userId, array $meta = []): void
    {
        if (!in_array($type, ['in', 'out'], true)) {
            throw new \InvalidArgumentException('Invalid stock movement type.');
        }

        if ($type === 'out' && $product->stock < $quantity) {
            throw ValidationException::withMessages(['stock' => "Insufficient stock for {$product->name}."]);
        }

        $newStock = $type === 'in' ? $product->stock + $quantity : $product->stock - $quantity;

        $this->db->transaction(function () use ($product, $quantity, $type, $userId, $newStock, $meta) {
            $product->update(['stock' => $newStock]);

            $this->movements->create([
                'product_id' => $product->id,
                'created_by' => $userId,
                'movementable_type' => $meta['movementable_type'] ?? null,
                'movementable_id' => $meta['movementable_id'] ?? null,
                'type' => $type,
                'quantity' => $quantity,
                'unit_cost' => $meta['unit_cost'] ?? null,
                'unit_price' => $meta['unit_price'] ?? null,
                'remarks' => $meta['remarks'] ?? null,
            ]);

            // Refresh product to get updated stock
            $product->refresh();

            // Check for low stock alerts
            $this->alertService->checkAndCreateAlert($product);
        });
    }
}
