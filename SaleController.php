<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Repositories\ProductRepository;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function __construct(
        protected ProductRepository $products,
        protected SaleService $sales,
    ) {
    }

    public function pos()
    {
        $products = $this->products->allWithRelations()->get();
        $lowStockProducts = $this->products->lowStock();

        return view('sales.pos', compact('products', 'lowStockProducts'));
    }

    public function checkout(CheckoutRequest $request)
    {
        $sale = $this->sales->createSale($request->validated()['items'], Auth::id(), $request->input('notes'));

        return redirect()->route('sales.pos')->with('success', 'Sale recorded successfully. Invoice: '.$sale->invoice_number);
    }

    public function history()
    {
        $sales = $this->sales->getHistory();

        return view('sales.history', compact('sales'));
    }

    public function barcodeSearch(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);

        $product = $this->products->findByBarcode($request->barcode);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'stock' => $product->stock,
        ]);
    }
}
