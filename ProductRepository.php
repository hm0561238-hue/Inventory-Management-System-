<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function allWithRelations()
    {
        return Product::with(['category', 'supplier'])
            ->select(['id', 'name', 'sku', 'barcode', 'category_id', 'supplier_id', 'price', 'cost', 'stock', 'created_at', 'updated_at'])
            ->orderBy('name');
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    public function find(int $id): ?Product
    {
        return Product::with(['category', 'supplier', 'stockMovements'])->find($id);
    }

    public function findByBarcode(string $barcode): ?Product
    {
        return Product::where('barcode', $barcode)
            ->with(['category', 'supplier'])
            ->first();
    }

    public function lowStock()
    {
        $threshold = config('inventory.stock_alert_threshold', 5);
        return Product::where('stock', '<', $threshold)
            ->select(['id', 'name', 'stock', 'category_id'])
            ->with('category')
            ->orderBy('stock')
            ->get();
    }

    public function filterByCategory(int $categoryId)
    {
        return Product::where('category_id', $categoryId)
            ->with(['supplier', 'category'])
            ->orderBy('name')
            ->get();
    }

    public function filterBySupplier(int $supplierId)
    {
        return Product::where('supplier_id', $supplierId)
            ->with(['category', 'supplier'])
            ->orderBy('name')
            ->get();
    }

    public function searchByName(string $name)
    {
        return Product::where('name', 'like', "%{$name}%")
            ->orWhere('barcode', 'like', "%{$name}%")
            ->orWhere('sku', 'like', "%{$name}%")
            ->with(['category', 'supplier'])
            ->orderBy('name')
            ->get();
    }

    public function getStockValue()
    {
        return Product::select(['id', 'name', 'price', 'cost', 'stock'])
            ->with('category')
            ->orderBy('name')
            ->get();
    }
}
