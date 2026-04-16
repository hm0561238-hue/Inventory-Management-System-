<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use App\Models\LowStockAlert;

class ReportService
{
    public function __construct(
        protected ReportRepository $reports,
        protected StockAlertService $alertService,
    ) {
    }

    public function getDashboardReports(): array
    {
        return [
            'todaySales' => optional($this->reports->dailySales(1)->first())->total ?? 0,
            'dailySales' => $this->reports->dailySales(14),
            'revenueTrend' => $this->reports->revenueTrend(),
            'topProducts' => $this->reports->topSellingProducts(8),
            'chartData' => $this->getChartData(),
            'alerts' => $this->alertService->getActiveAlerts(),
            'alertCount' => $this->alertService->getActiveAlertCount(),
            'summary' => $this->reports->salesSummary(),
        ];
    }

    /**
     * Get comprehensive chart data
     */
    public function getChartData(): array
    {
        return [
            'hourlyRevenue' => $this->reports->hourlyRevenue(),
            'weeklyRevenue' => $this->reports->weeklyRevenue(),
            'monthlyRevenue' => $this->reports->monthlyRevenue(),
            'categoryPerformance' => $this->reports->categoryPerformance(),
            'topSalespeople' => $this->reports->topSalespeople(),
            'productMetrics' => $this->reports->productMetrics(),
        ];
    }

    /**
     * Get detailed analytics for a date range
     */
    public function getAnalytics(?string $startDate = null, ?string $endDate = null): array
    {
        return [
            'summary' => $this->reports->salesSummary($startDate, $endDate),
            'hourlyRevenue' => $this->reports->hourlyRevenue(),
            'weeklyRevenue' => $this->reports->weeklyRevenue(),
            'topProducts' => $this->reports->topSellingProducts(15),
            'categoryPerformance' => $this->reports->categoryPerformance(),
            'topSalespeople' => $this->reports->topSalespeople(15),
        ];
    }

    /**
     * Get alert dashboard data
     */
    public function getAlertDashboard(): array
    {
        return [
            'activeAlerts' => $this->alertService->getActiveAlerts(),
            'lowStockProducts' => $this->alertService->getLowStockProducts(20),
            'alertCount' => $this->alertService->getActiveAlertCount(),
            'statusBreakdown' => [
                'active' => LowStockAlert::active()->count(),
                'resolved' => LowStockAlert::resolved()->count(),
            ],
        ];
    }
}
