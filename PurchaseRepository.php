<?php

namespace App\Repositories;

use App\Models\Purchase;

class PurchaseRepository
{
    public function create(array $data): Purchase
    {
        return Purchase::create($data);
    }

    public function withItems()
    {
        return Purchase::with(['items.product', 'supplier', 'user']);
    }

    public function find(int $id): ?Purchase
    {
        return Purchase::find($id);
    }
}
