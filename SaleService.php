<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\SaleRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    public function __construct(
        protected ProductRepository $products,
        protected SaleRepository $sales,
        protected InventoryService $inventory,
        protected DatabaseManager $db,
    ) {
    }

    public function getHistory(int $perPage = 15)
    {
        return $this->sales->getHistory($perPage);
    }

    public function createSale(array $items, int $userId, ?string $notes = null): Sale
    {
        return $this->db->transaction(function () use ($items, $userId, $notes) {
            $sale = $this->sales->create([
                'user_id' => $userId,
                'invoice_number' => $this->generateInvoiceNumber(),
                'total' => 0,
                'sold_at' => now(),
                'status' => 'completed',
                'notes' => $notes,
            ]);

            $total = 0;

            foreach ($items as $item) {
                $product = $this->products->find($item['product_id']);
                if (!$product) {
                    throw new \InvalidArgumentException('Product not found.');
                }

                $quantity = (int) $item['quantity'];
                $lineTotal = $product->price * $quantity;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $lineTotal,
                ]);

                $this->inventory->adjustStock($product, $quantity, 'out', $userId, [
                    'movementable_type' => Sale::class,
                    'movementable_id' => $sale->id,
                    'unit_price' => $product->price,
                    'remarks' => 'Sale checkout',
                ]);

                $total += $lineTotal;
            }

            $sale->update(['total' => $total]);

            return $sale;
        });
    }

    protected function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));
    }
}
