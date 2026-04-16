<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\View\View;

class StockLedgerController extends Controller
{
    // Middleware is applied via routes

    public function index(): View
    {
        $movements = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $products = Product::select('id', 'name', 'stock')->orderBy('name')->get();

        return view('stock.ledger.index', compact('movements', 'products'));
    }

    public function productLedger(Product $product): View
    {
        $movements = $product->stockMovements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $summary = [
            'current_stock' => $product->stock,
            'total_in' => $product->stockMovements()->where('type', 'in')->sum('quantity'),
            'total_out' => $product->stockMovements()->where('type', 'out')->sum('quantity'),
        ];

        return view('stock.ledger.product', compact('product', 'movements', 'summary'));
    }

    public function filter(string $type = null, string $productId = null): View
    {
        $query = StockMovement::with(['product', 'user']);

        if ($type && in_array($type, ['in', 'out'])) {
            $query->where('type', $type);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(50);
        $products = Product::select('id', 'name')->orderBy('name')->get();
        $selectedType = $type;
        $selectedProductId = $productId;

        return view('stock.ledger.index', compact('movements', 'products', 'selectedType', 'selectedProductId'));
    }
}
