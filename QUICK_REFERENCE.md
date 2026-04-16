# Quick Reference Guide - POS System Features

## 🚀 Quick Start Commands

```bash
# Install dependencies
composer install && npm install

# Run migrations (creates new tables)
php artisan migrate

# Build assets
npm run build

# Start development server
npm run dev
```

## 📊 Main Features at a Glance

| Feature | Access | Route | Purpose |
|---------|--------|-------|---------|
| **Dashboard** | All | `/` | Main overview with charts |
| **POS Sale** | Cashier+ | `/sales/pos` | Process sales transactions |
| **Barcode Scanner** | Cashier+ | `/barcode` | Scan products by barcode |
| **Products** | Admin | `/products` | Manage product inventory |
| **Alerts** | Admin | `/alerts` | View low stock alerts |
| **Reports** | Admin | `/reports` | Sales analytics dashboard |
| **Analytics** | Admin | `/reports/analytics` | Detailed analytics |

## 🔌 API Endpoints Quick Reference

### Barcode API
```
POST   /api/barcode/search        → Search product
GET    /api/barcode/statistics    → Get stats
GET    /api/barcode/recent        → Recent scans
```

### Alerts API
```
GET    /api/alerts                → All alerts
POST   /api/alerts/{id}/resolve   → Resolve alert
POST   /api/alerts/resolve-bulk   → Bulk resolve
```

### Report API
```
GET    /api/reports/chart-data         → Chart data
GET    /reports/export-csv            → Sales CSV
GET    /reports/export-detailed-csv   → Detailed CSV
GET    /reports/export-alerts-csv     → Alerts CSV
GET    /reports/export-pdf            → Sales PDF
GET    /reports/export-inventory-pdf  → Inventory PDF
```

## 📁 Important Files

### Models
- `app/Models/Product.php` - Products with stock tracking
- `app/Models/Sale.php` - Sales transactions
- `app/Models/LowStockAlert.php` - Stock alerts
- `app/Models/BarcodeScan.php` - Barcode scan logs
- `app/Models/User.php` - Users with roles

### Services
- `app/Services/SaleService.php` - Handle sales
- `app/Services/InventoryService.php` - Stock management
- `app/Services/StockAlertService.php` - Alert management
- `app/Services/BarcodeService.php` - Barcode lookup
- `app/Services/ReportService.php` - Analytics

### Controllers
- `app/Http/Controllers/SaleController.php` - POS operations
- `app/Http/Controllers/ProductController.php` - Product CRUD
- `app/Http/Controllers/ReportController.php` - Report generation
- `app/Http/Controllers/StockAlertController.php` - Alert APIs
- `app/Http/Controllers/BarcodeController.php` - Barcode APIs

### Views
- `resources/views/dashboard.blade.php` - Main dashboard
- `resources/views/alerts/index.blade.php` - Alert management
- `resources/views/barcode/index.blade.php` - Barcode scanner
- `resources/views/reports/analytics.blade.php` - Analytics
- `resources/views/reports/pdf.blade.php` - Sales PDF
- `resources/views/reports/inventory-pdf.blade.php` - Inventory PDF

## 👥 User Roles

### Admin (`role: 'admin'`)
- Access all features
- Manage products, purchases, stock
- View reports and analytics
- Manage alerts
- Export data

### Cashier (`role: 'cashier'`)
- Process sales
- Scan barcodes
- View own sales history
- View dashboard (read-only)

## 🎯 Common Tasks

### Create a Sale
1. Go to `/sales/pos`
2. Search/scan products
3. Add quantities
4. Click checkout
5. Stock auto-decrements

### Create Product
1. Admin → Products → Create
2. Fill in: Name, SKU, Barcode, Price
3. Set low stock threshold
4. Assign category & supplier
5. Save

### Set Low Stock Alert
When creating/editing product:
- Set `low_stock_threshold` (e.g., 10)
- Alert auto-creates when stock falls below
- Dashboard shows alert count

### Export Sales Report
1. Admin → Reports
2. Select date range (optional)
3. Choose export type:
   - CSV Summary
   - CSV Detailed
   - PDF Report
4. Download file

### View Alerts
1. Admin → Alerts
2. See all active low stock alerts
3. Click "Resolve" to mark complete
4. Or bulk resolve multiple
5. Export as CSV

### Use Barcode Scanner
1. Go to `/barcode`
2. Place cursor in input field
3. Scan barcode (physical scanner or keyboard input)
4. System shows product details
5. View statistics and recent scans

## 💾 Database Tables

### New Tables
```sql
low_stock_alerts
├── id, product_id, threshold
├── current_stock, status
├── notified_at, resolved_at
└── timestamps

barcode_scans
├── id, user_id, product_id
├── barcode, status, error_message
└── timestamps
```

### Updated Tables
```sql
products - Added low_stock_threshold column
```

## 🔒 Security

- All routes require authentication
- Admin routes require `role:admin` middleware
- Sensitive operations use database transactions
- CSRF protection on all forms
- Password hashing for users
- Barcode scans logged for audit trail

## 📱 Dashboard Charts

1. **Revenue Trend** - 30-day line chart
2. **Hourly Sales** - Today's bar chart
3. **Top Products** - Doughnut chart
4. **Category Performance** - Horizontal bar chart
5. **Weekly Revenue** - Multi-week analysis
6. **Monthly Revenue** - Long-term trend

## ⚙️ Configuration

### Default Low Stock Threshold: 10 units
Edit per product in Admin → Products → Edit

### Chart Colors
Located in view files - customize as needed

### Export Format
- CSV: UTF-8, streaming response
- PDF: A4 portrait, DOMPDF

## 🐛 Troubleshooting

### Charts not showing?
- Run `npm run build`
- Check Chart.js is loaded
- Verify data in console

### Alerts not creating?
- Check product has `low_stock_threshold` set
- Verify stock is below threshold
- Run migration: `php artisan migrate`

### Barcode search returns nothing?
- Verify product has barcode or SKU
- Check exact match for input
- Try SKU if barcode missing

### Exports not downloading?
- Check file permissions
- Verify DOMPDF installed: `composer require dompdf/dompdf`
- Check server memory limits

## 📞 Support Resources

- **FEATURES_GUIDE.md** - Complete feature documentation
- **ARCHITECTURE_GUIDE.md** - System design details
- **QUICK_START.md** - Getting started guide
- **IMPLEMENTATION_COMPLETED.md** - Implementation summary

## 🎓 Learning Path

1. Start with dashboard (overview)
2. Try POS sale (core feature)
3. Scan a barcode (scanner)
4. Create product (admin)
5. Check alerts (inventory)
6. Export report (analytics)

---

**Ready to use! Happy selling! 🎉**
