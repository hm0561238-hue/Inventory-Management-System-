<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;

class ProductService
{
    public function __construct(
        protected ProductRepository $repository,
        protected InventoryService $inventory,
    ) {
    }

    public function listProducts()
    {
        return $this->repository->allWithRelations()->get();
    }

    public function createProduct(array $data, int $userId): Product
    {
        $stock = $data['stock'] ?? 0;
        $data['stock'] = 0;

        $product = $this->repository->create($data);

        if ($stock > 0) {
            $this->inventory->adjustStock($product, $stock, 'in', $userId, [
                'unit_cost' => $data['cost'] ?? null,
                'remarks' => 'Initial stock added on product creation',
            ]);
        }

        return $product;
    }

    public function updateProduct(Product $product, array $data, int $userId): Product
    {
        $stockChange = $data['stock'] ?? $product->stock;
        $quantityDiff = $stockChange - $product->stock;

        if ($quantityDiff !== 0) {
            $movementType = $quantityDiff > 0 ? 'in' : 'out';
            $quantity = abs($quantityDiff);
            unset($data['stock']);

            $this->inventory->adjustStock($product, $quantity, $movementType, $userId, [
                'unit_cost' => $data['cost'] ?? $product->cost,
                'unit_price' => $data['price'] ?? $product->price,
                'remarks' => 'Stock adjusted during product update',
            ]);
        }

        return $this->repository->update($product, $data);
    }
}
