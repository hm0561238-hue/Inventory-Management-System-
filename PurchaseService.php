<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseService
{
    public function __construct(
        protected ProductRepository $products,
        protected PurchaseRepository $purchases,
        protected InventoryService $inventory,
        protected DatabaseManager $db,
    ) {
    }

    public function createPurchase(array $payload, int $userId): Purchase
    {
        return $this->db->transaction(function () use ($payload, $userId) {
            $purchase = $this->purchases->create([
                'supplier_id' => $payload['supplier_id'],
                'created_by' => $userId,
                'reference_number' => $this->generateReferenceNumber(),
                'purchase_date' => $payload['purchase_date'],
                'total' => 0,
                'notes' => $payload['notes'] ?? null,
            ]);

            $total = 0;

            foreach ($payload['items'] as $item) {
                $product = $this->products->find($item['product_id']);
                if (!$product) {
                    throw new \InvalidArgumentException('Product not found.');
                }

                $quantity = (int) $item['quantity'];
                $lineCost = $item['unit_cost'] * $quantity;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $lineCost,
                ]);

                $this->inventory->adjustStock($product, $quantity, 'in', $userId, [
                    'movementable_type' => Purchase::class,
                    'movementable_id' => $purchase->id,
                    'unit_cost' => $item['unit_cost'],
                    'remarks' => 'Purchase order received',
                ]);

                $total += $lineCost;
            }

            $purchase->update(['total' => $total]);

            return $purchase;
        });
    }

    protected function generateReferenceNumber(): string
    {
        return 'PO-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
    }
}
