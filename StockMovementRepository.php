<?php

namespace App\Repositories;

use App\Models\StockMovement;

class StockMovementRepository
{
    public function create(array $data): StockMovement
    {
        return StockMovement::create($data);
    }

    public function forProduct(int $productId)
    {
        return StockMovement::where('product_id', $productId)->orderBy('created_at', 'desc');
    }
}
