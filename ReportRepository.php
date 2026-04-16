<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
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

    public function topSellingProducts(int $limit = 10)
    {
        return Product::select('products.id', 'products.name')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->selectRaw('SUM(sale_items.quantity) as quantity_sold, SUM(sale_items.total_price) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('quantity_sold')
            ->limit($limit)
            ->get();
    }

    /**
     * Get hourly sales data for charts
     */
    public function hourlyRevenue(?string $date = null)
    {
        $date = $date ?? now()->toDateString();

        return Sale::selectRaw('HOUR(sold_at) as hour, SUM(total) as revenue, COUNT(*) as transactions')
            ->whereDate('sold_at', $date)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    /**
     * Get weekly revenue trend
     */
    public function weeklyRevenue(int $weeks = 8)
    {
        return Sale::selectRaw('DATE_FORMAT(sold_at, "%Y-%W") as week, SUM(total) as revenue, COUNT(*) as transactions')
            ->whereBetween('sold_at', [now()->subWeeks($weeks), now()])
            ->groupBy('week')
            ->orderBy('week')
            ->get();
    }

    /**
     * Get monthly revenue trend
     */
    public function monthlyRevenue(int $months = 12)
    {
        return Sale::selectRaw('DATE_FORMAT(sold_at, "%Y-%m") as month, SUM(total) as revenue, COUNT(*) as transactions')
            ->whereBetween('sold_at', [now()->subMonths($months), now()])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get product category performance
     */
    public function categoryPerformance()
    {
        return DB::table('categories')
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->selectRaw('categories.name, COUNT(sale_items.id) as total_items, SUM(sale_items.total_price) as revenue')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();
    }

    /**
     * Get top salespeople
     */
    public function topSalespeople(int $limit = 10)
    {
        return Sale::select('users.id', 'users.name')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->selectRaw('COUNT(*) as transactions, SUM(sales.total) as revenue')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    /**
     * Get sales summary for dashboard
     */
    public function salesSummary(?string $startDate = null, ?string $endDate = null)
    {
        $startDate = $startDate ?? now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? now()->toDateString();

        $sales = Sale::whereBetween('sold_at', [$startDate, $endDate])->get();

        return [
            'total_revenue' => $sales->sum('total'),
            'total_transactions' => $sales->count(),
            'average_transaction' => $sales->count() > 0 ? $sales->sum('total') / $sales->count() : 0,
            'unique_customers' => $sales->unique('user_id')->count(),
        ];
    }

    /**
     * Get product performance metrics
     */
    public function productMetrics(int $limit = 20)
    {
        return Product::select('products.id', 'products.name', 'products.price', 'products.stock')
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->selectRaw(
                'COALESCE(SUM(sale_items.quantity), 0) as total_sold,
                 COALESCE(SUM(sale_items.total_price), 0) as revenue,
                 products.stock as stock_level'
            )
            ->groupBy('products.id', 'products.name', 'products.price', 'products.stock')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }
}
