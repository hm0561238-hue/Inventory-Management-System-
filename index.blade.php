@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card p-3">
            <h5>Today's Total Sales</h5>
            <p class="display-6">Rs {{ number_format($todaySales, 2) }}</p>
        </div>
    </div>
    <div class="col-md-6 d-flex align-items-center justify-content-end gap-2">
        <a href="{{ route('reports.export.csv') }}" class="btn btn-outline-primary">Export CSV</a>
        <a href="{{ route('reports.export.pdf') }}" class="btn btn-outline-secondary">Export PDF</a>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card p-3">
            <h5>Daily Sales</h5>
            <canvas id="daily-sales-chart"></canvas>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card p-3">
            <h5>Revenue Trends</h5>
            <canvas id="revenue-trend-chart"></canvas>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card p-3">
            <h5>Top Selling Products</h5>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->quantity_sold }}</td>
                                <td>Rs {{ number_format($product->revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No sales data available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dailySalesLabels = @json($dailySales->pluck('date'));
const dailySalesData = @json($dailySales->pluck('total'));
const revenueTrendLabels = @json($revenueTrend->pluck('date'));
const revenueTrendData = @json($revenueTrend->pluck('total'));

new Chart(document.getElementById('daily-sales-chart'), {
    type: 'bar',
    data: {
        labels: dailySalesLabels,
        datasets: [{
            label: 'Total Sales',
            data: dailySalesData,
            backgroundColor: 'rgba(13, 110, 253, 0.6)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 1,
        }],
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } },
});

new Chart(document.getElementById('revenue-trend-chart'), {
    type: 'line',
    data: {
        labels: revenueTrendLabels,
        datasets: [{
            label: 'Revenue',
            data: revenueTrendData,
            backgroundColor: 'rgba(25, 135, 84, 0.4)',
            borderColor: 'rgba(25, 135, 84, 1)',
            tension: 0.2,
            fill: true,
        }],
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } },
});
</script>
@endpush
