@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold">Reports & Analytics</h1>
        <p class="text-gray-600">Comprehensive sales and inventory analytics</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-4 py-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-4 py-2 border rounded">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Filter
            </button>
        </form>
    </div>

    <!-- Export Options -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h2 class="font-bold mb-4">📥 Export Reports</h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <a href="{{ route('reports.export.csv', request()->all()) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">
                📊 Summary CSV
            </a>
            <a href="{{ route('reports.export.detailed.csv', request()->all()) }}" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-center">
                📈 Detailed CSV
            </a>
            <a href="{{ route('reports.export.pdf', request()->all()) }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-center">
                📄 PDF Report
            </a>
            <a href="{{ route('reports.export.inventory.pdf') }}" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 text-center">
                📦 Inventory PDF
            </a>
            <a href="{{ route('reports.export.alerts.csv') }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-center">
                ⚠️ Alerts CSV
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 font-medium">Total Revenue</h3>
            <p class="text-3xl font-bold text-blue-600 mt-2">₹{{ number_format($analytics['summary']['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 font-medium">Total Transactions</h3>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $analytics['summary']['total_transactions'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 font-medium">Average Transaction</h3>
            <p class="text-3xl font-bold text-purple-600 mt-2">₹{{ number_format($analytics['summary']['average_transaction'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 font-medium">Unique Customers</h3>
            <p class="text-3xl font-bold text-pink-600 mt-2">{{ $analytics['summary']['unique_customers'] }}</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Weekly Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Weekly Revenue</h2>
            <canvas id="weeklyChart" height="80"></canvas>
        </div>

        <!-- Category Performance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Category Performance</h2>
            <canvas id="categoryChart" height="80"></canvas>
        </div>
    </div>

    <!-- Top Products & Salespeople -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Top Products</h2>
            <div class="space-y-4">
                @foreach($analytics['topProducts'] as $product)
                <div class="flex justify-between items-center pb-2 border-b">
                    <div>
                        <p class="font-semibold">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->quantity_sold ?? 0 }} sold</p>
                    </div>
                    <p class="font-bold">₹{{ number_format($product->revenue ?? 0, 2) }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Salespeople -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Top Salespeople</h2>
            <div class="space-y-4">
                @foreach($analytics['topSalespeople'] as $person)
                <div class="flex justify-between items-center pb-2 border-b">
                    <div>
                        <p class="font-semibold">{{ $person->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $person->transactions ?? 0 }} transactions</p>
                    </div>
                    <p class="font-bold">₹{{ number_format($person->revenue ?? 0, 2) }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    @if($alerts['activeAlerts']->count() > 0)
    <div class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">⚠️ {{ $alerts['activeAlerts']->count() }} Active Low Stock Alerts</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($alerts['activeAlerts']->take(6) as $alert)
            <div class="bg-white rounded p-4">
                <p class="font-semibold">{{ $alert->product->name }}</p>
                <p class="text-sm text-gray-600">Stock: <strong class="text-red-600">{{ $alert->current_stock }}</strong> / {{ $alert->threshold }}</p>
                <a href="{{ route('alerts.index') }}" class="text-blue-600 text-sm hover:underline mt-2 block">View All →</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    // Weekly Revenue Chart
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    const weeklyData = @json($analytics['weeklyRevenue'] ?? []);

    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: weeklyData.map(d => d.week || d.date),
            datasets: [{
                label: 'Revenue',
                data: weeklyData.map(d => d.revenue || 0),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json($analytics['categoryPerformance'] ?? []);

    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: categoryData.map(c => c.name),
            datasets: [{
                label: 'Revenue',
                data: categoryData.map(c => c.revenue || 0),
                backgroundColor: '#f59e0b'
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } }
        }
    });
</script>
@endpush

@endsection
