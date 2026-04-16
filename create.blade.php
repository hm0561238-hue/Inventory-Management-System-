@extends('layouts.app')

@section('title', 'New Purchase Order')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-4">Create Purchase Order</h1>

            <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="form-select" required>
                            <option value="">Select supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="purchase_date" class="form-label">Purchase Date</label>
                        <input id="purchase_date" name="purchase_date" type="date" class="form-control" value="{{ old('purchase_date', now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="notes" class="form-label">Notes</label>
                        <input id="notes" name="notes" type="text" class="form-control" value="{{ old('notes') }}" placeholder="Order notes (optional)">
                    </div>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">Items</h2>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="add-item">Add Item</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="items-table">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="items[0][product_id]" class="form-select" required>
                                        <option value="">Select product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input name="items[0][quantity]" type="number" class="form-control" min="1" value="1" required></td>
                                <td><input name="items[0][unit_cost]" type="number" step="0.01" class="form-control" min="0" value="0.00" required></td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-item">Remove</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary">Save Purchase Order</button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const productOptions = @json($products->map(fn($product) => ['id' => $product->id, 'name' => $product->name]));
            const itemsTable = document.getElementById('items-table').querySelector('tbody');
            const addItemButton = document.getElementById('add-item');

            const renderProductOptions = () => {
                let html = '<option value="">Select product</option>';
                productOptions.forEach(option => {
                    html += `<option value="${option.id}">${option.name}</option>`;
                });
                return html;
            };

            let rowIndex = 1;

            addItemButton.addEventListener('click', () => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <select name="items[${rowIndex}][product_id]" class="form-select" required>
                            ${renderProductOptions()}
                        </select>
                    </td>
                    <td><input name="items[${rowIndex}][quantity]" type="number" class="form-control" min="1" value="1" required></td>
                    <td><input name="items[${rowIndex}][unit_cost]" type="number" step="0.01" class="form-control" min="0" value="0.00" required></td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-item">Remove</button>
                    </td>
                `;

                itemsTable.appendChild(row);
                rowIndex++;
            });

            itemsTable.addEventListener('click', (event) => {
                if (event.target.matches('.remove-item')) {
                    const row = event.target.closest('tr');
                    if (row && itemsTable.rows.length > 1) {
                        row.remove();
                    }
                }
            });
        </script>
    @endpush
@endsection
