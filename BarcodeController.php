<?php

namespace App\Http\Controllers;

use App\Services\BarcodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BarcodeController extends Controller
{
    public function __construct(protected BarcodeService $barcodeService)
    {
    }

    /**
     * Search for product by barcode - API endpoint
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'barcode' => 'required|string|min:1|max:100',
        ]);

        $result = $this->barcodeService->searchByBarcode(
            $validated['barcode'],
            auth()->id()
        );

        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Display barcode scanning interface
     */
    public function index()
    {
        return view('barcode.index');
    }

    /**
     * Get scan statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $days = $request->input('days', 7);
        $userId = $request->input('include_all') ? null : auth()->id();

        $stats = $this->barcodeService->getScanStatistics($userId, $days);

        return response()->json($stats);
    }

    /**
     * Get recent scans
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 20);
        $scans = $this->barcodeService->getRecentScans(auth()->id(), $limit);

        return response()->json($scans->map(fn ($scan) => [
            'id' => $scan->id,
            'barcode' => $scan->barcode,
            'product_name' => $scan->product?->name,
            'status' => $scan->status,
            'created_at' => $scan->created_at->toDateTimeString(),
        ]));
    }
}
