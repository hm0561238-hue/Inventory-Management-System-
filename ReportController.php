<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\StockAlertService;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Dompdf\Dompdf;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected StockAlertService $alertService,
    ) {
    }

    public function index()
    {
        $reports = $this->reportService->getDashboardReports();

        return view('reports.index', [
            'todaySales' => $reports['todaySales'],
            'dailySales' => $reports['dailySales'],
            'revenueTrend' => $reports['revenueTrend'],
            'topProducts' => $reports['topProducts'],
            'chartData' => $reports['chartData'],
            'alerts' => $reports['alerts'],
            'alertCount' => $reports['alertCount'],
            'summary' => $reports['summary'],
        ]);
    }

    /**
     * Display detailed analytics dashboard
     */
    public function analytics(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $analytics = $this->reportService->getAnalytics($startDate, $endDate);
        $alerts = $this->reportService->getAlertDashboard();

        return view('reports.analytics', [
            'analytics' => $analytics,
            'alerts' => $alerts,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Get chart data via AJAX
     */
    public function chartData(Request $request): JsonResponse
    {
        $type = $request->input('type', 'hourly');

        return response()->json($this->reportService->getChartData());
    }

    /**
     * Export sales data as CSV
     */
    public function exportCsv(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sale::with('user', 'items');

        if ($startDate) {
            $query->whereDate('sold_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('sold_at', '<=', $endDate);
        }

        $sales = $query->orderBy('sold_at', 'desc')->get();
        $filename = 'sales-report-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Sale ID', 'Invoice', 'Cashier', 'Total', 'Items', 'Date']);

            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->id,
                    $sale->invoice_number,
                    $sale->user->name,
                    number_format($sale->total, 2),
                    $sale->items->count(),
                    $sale->sold_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export detailed sales report as CSV
     */
    public function exportDetailedCsv(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sale::with('user', 'items.product');

        if ($startDate) {
            $query->whereDate('sold_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('sold_at', '<=', $endDate);
        }

        $sales = $query->orderBy('sold_at', 'desc')->get();
        $filename = 'sales-detailed-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Sale ID', 'Invoice', 'Cashier', 'Date', 'Product', 'Quantity', 'Unit Price', 'Total']);

            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    fputcsv($handle, [
                        $sale->id,
                        $sale->invoice_number,
                        $sale->user->name,
                        $sale->sold_at->format('Y-m-d H:i:s'),
                        $item->product->name,
                        $item->quantity,
                        number_format($item->unit_price, 2),
                        number_format($item->total_price, 2),
                    ]);
                }
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export sales data as PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sale::with(['user', 'items.product']);

        if ($startDate) {
            $query->whereDate('sold_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('sold_at', '<=', $endDate);
        }

        $sales = $query->orderBy('sold_at', 'desc')->get();
        $summary = [
            'total_sales' => $sales->sum('total'),
            'total_transactions' => $sales->count(),
            'average' => $sales->count() > 0 ? $sales->sum('total') / $sales->count() : 0,
        ];

        $html = view('reports.pdf', [
            'sales' => $sales,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="sales-report-' . date('Y-m-d') . '.pdf"',
        ]);
    }

    /**
     * Export inventory report as PDF
     */
    public function exportInventoryPdf()
    {
        $alerts = $this->alertService->getActiveAlerts();
        $lowStockProducts = $this->alertService->getLowStockProducts(100);

        $html = view('reports.inventory-pdf', [
            'alerts' => $alerts,
            'lowStockProducts' => $lowStockProducts,
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="inventory-report-' . date('Y-m-d') . '.pdf"',
        ]);
    }

    /**
     * Export inventory alerts as CSV
     */
    public function exportAlertsCsv(): \Illuminate\Http\Response
    {
        $alerts = $this->alertService->getActiveAlerts();
        $filename = 'stock-alerts-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($alerts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Product', 'SKU', 'Threshold', 'Current Stock', 'Status', 'Alert Date']);

            foreach ($alerts as $alert) {
                fputcsv($handle, [
                    $alert->product->name,
                    $alert->product->sku,
                    $alert->threshold,
                    $alert->current_stock,
                    ucfirst($alert->status),
                    $alert->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
