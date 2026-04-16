# 🚀 Quick Start Guide - Inventory & POS System

## Getting Started

### 1. Start the Development Server
```bash
php artisan serve
```
Access the app at: **http://localhost:8000**

### 2. Login Credentials

**Admin Account:**
- Email: `admin@example.com`
- Password: `password`

**Cashier Account:**
- Email: `cashier@example.com`
- Password: `password`

---

## For Admin Users

### Create a Product
1. Click **Products** in the left menu
2. Click **Add Product** button
3. Fill in the fields:
   - Product Name (required)
   - SKU (optional, unique)
   - Barcode (required, unique)
   - Price (per unit)
   - Cost (your cost per unit)
   - Stock Quantity (initial stock)
   - Category (optional dropdown)
   - Supplier (optional dropdown)
4. Click **Save Product**

**Note**: Creating a product with initial stock automatically creates a stock movement log entry.

### View Stock Ledger
1. Click **Stock Ledger** in the left menu
2. View all stock movements (in/out)
3. Filter by:
   - **Movement Type**: in/out or both
   - **Product**: Select specific product
4. Click **View Details** to see per-product history with summary

### Check Reports
1. Click **Reports** in the left menu
2. See:
   - Today's total sales
   - Daily sales chart (last 14 days)
   - Revenue trend chart
   - Top-selling products list
3. Export data:
   - **CSV Export**: Download as spreadsheet
   - **PDF Export**: Download as formatted report

### Manage Purchases
1. Click **Purchases** in the left menu
2. Click **Create Purchase Order**
3. Select supplier
4. Add items with quantities
5. Click **Save**

---

## For Cashier Users

### Use POS (Point of Sale)
1. Click **POS** or **Sales** in the menu
2. Add products to cart:
   - **Option A**: Click product name from the list
   - **Option B**: Use barcode scanner - enter/scan barcode in input field
3. Adjust quantities as needed
4. Click **Remove** to delete item from cart
5. When ready, click **Checkout**

### View Sales History
1. Click **Sales History** in the menu
2. See all sales with:
   - Sale date and time
   - Cashier who processed it
   - Total amount
   - Number of items
3. Click **View Items** to see individual products

---

## Low Stock Alerts

Products are marked as **low stock** when quantity falls below the threshold.

### Current Threshold: 5 units

To change the threshold:
1. Edit `.env` file
2. Set: `STOCK_ALERT_THRESHOLD=10` (or your desired number)
3. Run: `php artisan config:cache`

Low stock indicators appear in:
- ✚ Product listing page (red badge)
- ✚ POS screen (alert box at top)
- ✚ Dashboard summary

---

## Barcode Scanner Simulation

The barcode scanner on the POS screen simulates a physical scanner:

1. On the **POS** page, you'll see a **"Scan or Enter Barcode"** input field
2. Type or paste a barcode (e.g., `MOUSE001`)
3. Press **Enter**
4. Product automatically loads and is added to cart
5. Enter quantity and click **Add to Cart**

**Sample Barcodes** (from seeded data):
- `MOUSE001` - Wireless Mouse
- `KEYBOARD001` - USB Keyboard
- `MONITOR001` - Monitor 24"
- `STAND001` - Laptop Stand

---

## Reports & Exports

### Generate CSV Report
1. Go to **Reports**
2. Click **Export as CSV**
3. Opens/downloads: `sales-report-YYYY-MM-DD.csv`
4. Contains columns: Sale ID, Cashier, Total, Date

### Generate PDF Report
1. Go to **Reports**
2. Click **Export as PDF**
3. Opens/downloads: `sales-report-YYYY-MM-DD.pdf`
4. Formatted with headers, totals, and styling

---

## Common Tasks

### Add Initial Stock to Existing Product
1. Go to **Products**
2. Click **Edit** on the product
3. Increase **Stock Quantity**
4. Click **Update**
5. Check **Stock Ledger** - will show "in" movement automatically

### Verify Transaction Safety
When creating a sale:
- If barcode doesn't exist: Error message shows
- If stock insufficient: Error message shows
- If sale succeeds: Stock decrements and movement logged atomically

### Check Product Cost vs Price
1. Go to **Products**
2. Each product shows:
   - **Cost**: Your purchase price
   - **Price**: Your selling price
   - **Margin**: Automatically calculated (price - cost)

---

## Database Reset (Development Only)

To reset to initial state:
```bash
php artisan migrate:fresh --seed
```

This will:
1. Drop all tables
2. Re-create schema
3. Seed default users and sample data
4. Return system to fresh state

---

## Troubleshooting

### Issue: "SQLSTATE[HY000]"
**Solution**: Ensure database is running and .env has correct database credentials

### Issue: "Class not found" error
**Solution**: Run `composer dump-autoload`

### Issue: Barcode search returns not found
**Solution**: Ensure the barcode exists in products table. Check Products page to see available barcodes.

### Issue: "Unauthenticated" redirects
**Solution**: Login with credentials above. Session may have expired. Clear browser cookies if needed.

### Issue: Cannot access Stock Ledger or Reports (permission denied)
**Solution**: Ensure logged in as **admin@example.com**. Cashiers cannot access these pages.

---

## Key Features Reminder

✅ **Products**: Full CRUD with SKU, barcode, cost, price, category, supplier  
✅ **Stock Tracking**: Automatic ledger on every in/out movement  
✅ **Sales**: Multi-item transactions with invoice numbers  
✅ **Reports**: Charts, top products, revenue trends  
✅ **Barcode Scanner**: Simulation on POS screen  
✅ **Low Stock Alerts**: Configurable threshold  
✅ **Exports**: CSV and PDF format  
✅ **Role-Based Access**: Admin vs Cashier  
✅ **Transaction Safety**: Database transactions on sales  
✅ **Audit Trail**: Complete stock movement history  

---

## Support

For errors or issues, check:
1. Terminal output (running `php artisan serve`)
2. Browser console (F12) for client-side errors
3. `storage/logs/laravel.log` for detailed error logs
4. This IMPLEMENTATION_REPORT.md for feature details

---

**Happy selling! 🎉**
