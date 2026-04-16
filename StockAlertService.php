<?php

namespace App\Services;

use App\Models\LowStockAlert;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class StockAlertService
{
    public const DEFAULT_LOW_STOCK_THRESHOLD = 10;

    /**
     * Check if a product has low stock and create alert if needed
     */
    public function checkAndCreateAlert(Product $product): ?LowStockAlert
    {
        $threshold = $product->low_stock_threshold ?? self::DEFAULT_LOW_STOCK_THRESHOLD;

        // If stock is above threshold, resolve any active alerts
        if ($product->stock >= $threshold) {
            $this->resolveAlertsForProduct($product);
            return null;
        }

        // Check if an active alert already exists
        $existingAlert = LowStockAlert::where('product_id', $product->id)
            ->where('status', 'active')
            ->first();

        if ($existingAlert) {
            // Update the current stock
            $existingAlert->update(['current_stock' => $product->stock]);
            return $existingAlert;
        }

        // Create new alert
        return LowStockAlert::create([
            'product_id' => $product->id,
            'threshold' => $threshold,
            'current_stock' => $product->stock,
            'status' => 'active',
            'notified_at' => now(),
        ]);
    }

    /**
     * Get all active alerts
     */
    public function getActiveAlerts(): Collection
    {
        return LowStockAlert::active()
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get alerts for a specific product
     */
    public function getProductAlerts(Product $product): Collection
    {
        return LowStockAlert::where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Resolve all active alerts for a product
     */
    public function resolveAlertsForProduct(Product $product): void
    {
        LowStockAlert::where('product_id', $product->id)
            ->where('status', 'active')
            ->each(fn ($alert) => $alert->resolve());
    }

    /**
     * Get count of active alerts
     */
    public function getActiveAlertCount(): int
    {
        return LowStockAlert::active()->count();
    }

    /**
     * Get products with low stock
     */
    public function getLowStockProducts(int $limit = 10): Collection
    {
        return LowStockAlert::active()
            ->with('product')
            ->limit($limit)
            ->get()
            ->map(fn ($alert) => $alert->product);
    }

    /**
     * Resolve alert by ID
     */
    public function resolveAlert(int $alertId): void
    {
        $alert = LowStockAlert::findOrFail($alertId);
        $alert->resolve();
    }

    /**
     * Update low stock threshold for a product
     */
    public function setLowStockThreshold(Product $product, int $threshold): void
    {
        $product->update(['low_stock_threshold' => max(0, $threshold)]);
    }
}
