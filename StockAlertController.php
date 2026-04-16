<?php

namespace App\Http\Controllers;

use App\Services\StockAlertService;
use App\Models\LowStockAlert;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockAlertController extends Controller
{
    public function __construct(protected StockAlertService $alertService)
    {
    }

    /**
     * Display active alerts
     */
    public function index()
    {
        $alerts = $this->alertService->getActiveAlerts();

        return view('alerts.index', [
            'alerts' => $alerts,
            'alertCount' => $this->alertService->getActiveAlertCount(),
        ]);
    }

    /**
     * Get alerts via AJAX
     */
    public function getAlerts(): JsonResponse
    {
        $alerts = $this->alertService->getActiveAlerts();

        return response()->json([
            'count' => $alerts->count(),
            'alerts' => $alerts->map(fn ($alert) => [
                'id' => $alert->id,
                'product_name' => $alert->product->name,
                'product_id' => $alert->product->id,
                'threshold' => $alert->threshold,
                'current_stock' => $alert->current_stock,
                'created_at' => $alert->created_at->diffForHumans(),
            ]),
        ]);
    }

    /**
     * Resolve an alert
     */
    public function resolve(LowStockAlert $alert): JsonResponse
    {
        $this->authorize('admin');

        $alert->resolve();

        return response()->json([
            'success' => true,
            'message' => 'Alert resolved successfully',
        ]);
    }

    /**
     * Resolve multiple alerts
     */
    public function resolveBulk(Request $request): JsonResponse
    {
        $this->authorize('admin');

        $alertIds = $request->validate([
            'alert_ids' => 'required|array',
            'alert_ids.*' => 'integer|exists:low_stock_alerts,id',
        ])['alert_ids'];

        LowStockAlert::whereIn('id', $alertIds)->each(fn ($alert) => $alert->resolve());

        return response()->json([
            'success' => true,
            'message' => count($alertIds) . ' alerts resolved successfully',
        ]);
    }
}
