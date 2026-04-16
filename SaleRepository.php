<?php

namespace App\Repositories;

use App\Models\Sale;

class SaleRepository
{
    public function create(array $data): Sale
    {
        return Sale::create($data);
    }

    public function withItemsAndUser()
    {
        return Sale::with(['items.product', 'user']);
    }

    public function getHistory(int $perPage = 15)
    {
        return $this->withItemsAndUser()->latest('sold_at')->paginate($perPage);
    }

    public function getTodayTotal(): float
    {
        return Sale::whereDate('sold_at', today())->sum('total');
    }

    public function dailySales(int $days = 14)
    {
        return Sale::selectRaw('DATE(sold_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit($days)
            ->get();
    }

    public function revenueTrend()
    {
        return Sale::selectRaw('DATE(sold_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
