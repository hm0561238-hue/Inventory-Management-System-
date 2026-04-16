<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Repositories\PurchaseRepository;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService,
        protected PurchaseRepository $purchaseRepository,
    ) {
    }

    public function index(): View
    {
        $purchases = $this->purchaseRepository->withItems()->latest('purchase_date')->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $purchase = $this->purchaseService->createPurchase($request->validated(), Auth::id());

        return redirect()->route('purchases.index')->with('success', 'Purchase order created successfully. Ref: ' . $purchase->reference_number);
    }
}
