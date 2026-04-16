<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::firstOrCreate([
            'email' => 'cashier@example.com',
        ], [
            'name' => 'Cashier User',
            'password' => bcrypt('password'),
            'role' => 'cashier',
        ]);

        $accessories = Category::firstOrCreate([
            'slug' => 'accessories',
        ], [
            'name' => 'Accessories',
            'description' => 'Computer accessories',
        ]);

        $officeSupplies = Category::firstOrCreate([
            'slug' => 'office-supplies',
        ], [
            'name' => 'Office Supplies',
            'description' => 'Office essentials and stationery',
        ]);

        $gadgets = Category::firstOrCreate([
            'slug' => 'gadgets',
        ], [
            'name' => 'Gadgets',
            'description' => 'Electronic devices and gadgets',
        ]);

        $acme = Supplier::firstOrCreate([
            'email' => 'aisha@acme.com',
        ], [
            'name' => 'Acme Supplies',
            'contact_person' => 'Aisha Khan',
            'phone' => '03001234567',
            'address' => 'Karachi, Pakistan',
        ]);

        $officePro = Supplier::firstOrCreate([
            'email' => 'hamza@officepro.com',
        ], [
            'name' => 'Office Pro',
            'contact_person' => 'Hamza Ali',
            'phone' => '03007654321',
            'address' => 'Lahore, Pakistan',
        ]);

        Product::updateOrCreate([
            'barcode' => '10101010',
        ], [
            'name' => 'Wireless Mouse',
            'sku' => 'WM-100',
            'category_id' => $accessories->id,
            'supplier_id' => $acme->id,
            'cost' => 10.00,
            'price' => 19.99,
            'stock' => 12,
        ]);

        Product::updateOrCreate([
            'barcode' => '20202020',
        ], [
            'name' => 'USB Keyboard',
            'sku' => 'UK-200',
            'category_id' => $accessories->id,
            'supplier_id' => $officePro->id,
            'cost' => 14.00,
            'price' => 29.99,
            'stock' => 8,
        ]);

        Product::updateOrCreate([
            'barcode' => '30303030',
        ], [
            'name' => 'Monitor 24"',
            'sku' => 'MN-240',
            'category_id' => $gadgets->id,
            'supplier_id' => $officePro->id,
            'cost' => 95.00,
            'price' => 149.99,
            'stock' => 4,
        ]);

        Product::updateOrCreate([
            'barcode' => '40404040',
        ], [
            'name' => 'Laptop Stand',
            'sku' => 'LS-010',
            'category_id' => $officeSupplies->id,
            'supplier_id' => $acme->id,
            'cost' => 18.00,
            'price' => 35.00,
            'stock' => 15,
        ]);
    }
}
