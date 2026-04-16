# 🔧 Architecture & Developer Guide

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                     HTTP Requests                            │
├─────────────────────────────────────────────────────────────┤
│
├─→ routes/web.php (Route Definition)
│   ├─ Public: login, register, logout
│   ├─ Authenticated: dashboard, POS, sales history
│   └─ Admin: products, purchases, reports, stock-ledger
│
├─→ app/Http/Controllers (Request Handlers)
│   ├─ AuthController: User auth
│   ├─ ProductController: Product CRUD
│   ├─ SaleController: POS checkout
│   ├─ PurchaseController: Purchase orders
│   ├─ ReportController: Analytics
│   └─ StockLedgerController: Audit trails
│
├─→ app/Http/Requests (Input Validation)
│   ├─ StoreProductRequest
│   ├─ CheckoutRequest
│   ├─ StorePurchaseRequest
│   └─ Auth requests
│
├─→ app/Services (Business Logic)
│   ├─ ProductService: Product operations
│   ├─ SaleService: Sale creation with transactions
│   ├─ PurchaseService: Purchase operations
│   ├─ InventoryService: Stock adjustments
│   └─ ReportService: Report generation
│
├─→ app/Repositories (Data Access)
│   ├─ ProductRepository: Product queries
│   ├─ SaleRepository: Sale queries
│   ├─ PurchaseRepository: Purchase queries
│   ├─ StockMovementRepository: Movement queries
│   └─ ReportRepository: Report queries
│
├─→ app/Models (Database Layer)
│   ├─ User, Product, Sale, SaleItem
│   ├─ Purchase, PurchaseItem
│   ├─ Category, Supplier
│   ├─ StockMovement
│   └─ ActivityLog
│
└─→ Database
    ├─ MySQL/SQLite
    └─ 10 core tables + relationships
```

---

## Directory Structure

```
pos/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── ProductController.php
│   │   │   ├── SaleController.php
│   │   │   ├── PurchaseController.php
│   │   │   ├── ReportController.php
│   │   │   └── StockLedgerController.php
│   │   ├── Middleware/
│   │   │   └── CheckRole.php
│   │   └── Requests/
│   │       ├── StoreProductRequest.php
│   │       ├── CheckoutRequest.php
│   │       └── ... validation classes
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Sale.php
│   │   ├── SaleItem.php
│   │   ├── Purchase.php
│   │   ├── PurchaseItem.php
│   │   ├── Category.php
│   │   ├── Supplier.php
│   │   ├── StockMovement.php
│   │   └── ActivityLog.php
│   ├── Services/
│   │   ├── ProductService.php
│   │   ├── SaleService.php
│   │   ├── PurchaseService.php
│   │   ├── InventoryService.php
│   │   └── ReportService.php
│   └── Repositories/
│       ├── ProductRepository.php
│       ├── SaleRepository.php
│       ├── PurchaseRepository.php
│       ├── StockMovementRepository.php
│       └── ReportRepository.php
├── resources/
│   ├── views/
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   ├── products/
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── index.blade.php
│   │   ├── sales/
│   │   │   ├── pos.blade.php
│   │   │   └── history.blade.php
│   │   ├── purchases/
│   │   │   ├── create.blade.php
│   │   │   └── index.blade.php
│   │   ├── stock/
│   │   │   └── ledger/
│   │   │       ├── index.blade.php
│   │   │       └── product.blade.php
│   │   ├── reports/
│   │   │   ├── index.blade.php
│   │   │   └── pdf.blade.php
│   │   └── layouts/
│   │       └── app.blade.php
├── database/
│   ├── migrations/
│   │   ├── 2026_03_30_000001_create_products_table.php
│   │   ├── 2026_03_30_000002_create_sales_table.php
│   │   ├── ... (10+ migrations)
│   │   └── 2026_03_30_000012_create_activity_logs_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── config/
│   └── inventory.php
└── routes/
    └── web.php
```

---

## Key Design Patterns

### 1. Service Layer Pattern
Services encapsulate business logic, keeping controllers thin:

```php
// SaleService handles complex checkout logic
$saleService->createSale(user, items);

// InventoryService handles stock adjustments
inventoryService->adjustStock(product, quantity, type);
```

**Benefits**:
- Reusable business logic
- Easy testing
- Single responsibility principle

### 2. Repository Pattern
Repositories abstract database queries:

```php
// ProductRepository handles all product queries
$repo->allWithRelations();
$repo->findByBarcode($barcode);
$repo->filterByCategory($categoryId);
```

**Benefits**:
- Query logic centralized
- Easy to swap databases
- Testable without database

### 3. Request Validation Pattern
HTTP requests validated before controller receives them:

```php
// StoreProductRequest validates product input
$validated = StoreProductRequest->validated();
```

**Benefits**:
- Centralized validation rules
- Clear validation requirements
- Consistent error messages

### 4. Middleware Pattern
Role-based access control via middleware:

```php
Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/products', ...);
});
```

**Benefits**:
- Authorization before controller
- Reusable across routes
- Clear permission requirements

### 5. Polymorphic Relationships
Stock movements track different sources (sale, purchase, adjustment):

```php
// One movement can belong to sale OR purchase
StockMovement::morph to movementable (polymorphic)
```

**Benefits**:
- Flexible audit trail
- Single table for all movement types
- Easy to trace origin

---

## Database Relationships

### Product Relations
```
Product → Category (Foreign Key)
Product → Supplier (Foreign Key)
Product ← Sale_Item (One-to-Many)
Product ← Stock_Movement (One-to-Many)
```

### Sale Relations
```
Sale → User (Customer/Cashier)
Sale ← Sale_Item (One-to-Many)
Sale ← Stock_Movement (Polymorphic)
```

### StockMovement Relations
```
StockMovement → Product
StockMovement → User (Created by)
StockMovement → (Polymorphic) Sale or Purchase or Adjustment
```

---

## Transaction Safety

### Sale Checkout (Atomic)
```php
DB::transaction(function () {
    // 1. Create sale record
    $sale = Sale::create([...]);
    
    // 2. Create sale items
    foreach ($items as $item) {
        SaleItem::create([...]);
    }
    
    // 3. Decrement stock for each product
    foreach ($items as $item) {
        Product->decrement('stock', $item['quantity']);
        StockMovement::create([...]);
    }
    
    // All succeed or all rollback
});
```

**Guarantees**:
- Sale and items always created together
- Stock always matches ledger
- No partial transactions

---

## Query Optimization

### Eager Loading (Prevent N+1)
```php
// BAD: N+1 query problem
foreach (Product::all() as $product) {
    echo $product->category->name; // Query for each product
}

// GOOD: Eager loading
foreach (Product::with('category')->get() as $product) {
    echo $product->category->name; // Single query
}
```

### Selective Columns
```php
// Instead of: Product::with('category')->get()
// Use: Product::select('id', 'name', 'price', 'category_id')
//        ->with('category:id,name')->get()
```

**Impact**: Faster queries, less network traffic

### Database Indexes
```
✅ barcode (unique)
✅ sku (unique)
✅ category_id, supplier_id (composite)
✅ product_id on stock_movements
```

---

## Configuration

### Inventory Settings (config/inventory.php)
```php
'stock_alert_threshold' => env('STOCK_ALERT_THRESHOLD', 5),
'enable_notifications' => env('STOCK_NOTIFICATIONS_ENABLED', false),
'track_movements' => true,
'alert_email' => env('LOW_STOCK_ALERT_EMAIL', 'admin@example.com'),
```

### Using Configuration
```php
// In code:
$threshold = config('inventory.stock_alert_threshold');

// In .env:
STOCK_ALERT_THRESHOLD=10
```

---

## Adding New Features

### Adding a New Product Field

1. **Create Migration**:
```php
Schema::table('products', function (Blueprint $table) {
    $table->string('new_field')->nullable();
});
```

2. **Update Model**:
```php
protected $fillable = [..., 'new_field'];
```

3. **Update Request Validation**:
```php
public function rules() {
    return [..., 'new_field' => 'nullable|string'];
}
```

4. **Update Views**:
```blade
<input type="text" name="new_field" value="{{ $product->new_field }}">
```

5. **Update Controller** (if needed):
```php
$product = ProductService::createProduct($validated);
```

### Adding a New Report

1. **Add Method to ReportRepository**:
```php
public function newReport() { ... }
```

2. **Add Method to ReportService**:
```php
public function generateNewReport() { ... }
```

3. **Update ReportController**:
```php
public function newReport() {
    $data = ReportService::generateNewReport();
    return view('reports.new-report', $data);
}
```

4. **Create View** (`resources/views/reports/new-report.blade.php`)

5. **Add Route**:
```php
Route::get('/reports/new-report', 'ReportController@newReport');
```

---

## Testing Guide

### Test Checklist
```
Authentication:
□ Login with email or username
□ Login fails with wrong password
□ Logout clears session
□ Register validates unique email/username

Products:
□ Create product with all fields
□ Update product
□ Delete product
□ Barcode lookup works
□ SKU is optional
□ Barcode is unique

Stock:
□ Stock decrements on sale
□ Stock ledger records movement
□ Cannot sell more than stock
□ Stock increases on purchase

Sales:
□ Add multiple items to cart
□ Checkout creates sale and items
□ Invoice number generated
□ Stock movements created

Reports:
□ Dashboard shows today's sales
□ Charts render correctly
□ CSV export downloads
□ PDF export downloads

Permissions:
□ Admin can CRUD products
□ Cashier cannot access admin pages
□ Cashier can use POS
```

### Manual Testing
```bash
# 1. Start server
php artisan serve

# 2. Login as admin
admin@example.com / password

# 3. Create product
Navigate to /products, create "Test Product"

# 4. View stock ledger
Navigate to /stock-ledger, verify "in" movement

# 5. Use POS as cashier
Logout, login as cashier@example.com
Go to POS, add product, checkout

# 6. Verify stock decreased
Login as admin again, check product stock changed
```

---

## Debugging

### Enable Query Logging
```php
// In tinker or controller
DB::enableQueryLog();
// ... your code ...
dd(DB::getQueryLog());
```

### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Database Verification
```bash
php artisan tinker

# Check product was created
>>> Product::first()

# Check stock movement was logged
>>> StockMovement::latest()->first()

# Check sale
>>> Sale::with('items')->latest()->first()
```

---

## Performance Tips

1. **Use Eager Loading**: Always use `with()` in queries
2. **Limit Results**: Use `paginate()` for large datasets
3. **Cache Reports**: Consider caching report queries
4. **Database Indexes**: Create indexes on commonly filtered columns
5. **Optimize Queries**: Use `select()` to fetch only needed columns

---

## Deployment Checklist

```
Pre-Deployment:
□ .env configured for production database
□ APP_KEY set
□ APP_DEBUG=false
□ DATABASE backed up
□ Migrations tested locally

Deployment:
□ php artisan migrate --force
□ php artisan db:seed --class=DatabaseSeeder (if new DB)
□ php artisan config:cache
□ php artisan route:cache
□ Set proper file permissions
□ Configure email (if notifications enabled)

Post-Deployment:
□ Test login
□ Test product creation
□ Test checkout
□ Monitor logs for errors
```

---

## Common Gotchas

1. **Decimal Type**: Always cast currency fields as `decimal:2`
2. **N+1 Queries**: Always use `with()` for relationships
3. **Transaction Rollback**: Wrap multi-step operations in `DB::transaction()`
4. **Permission Checks**: Always verify user role before sensitive operations
5. **Unique Constraints**: Test unique validations in requests

---

**For more help, check IMPLEMENTATION_REPORT.md and QUICK_START.md**
