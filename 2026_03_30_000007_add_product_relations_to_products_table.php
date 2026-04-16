<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique()->after('id');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete()->after('barcode');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete()->after('category_id');
            $table->decimal('cost', 12, 2)->default(0)->after('price');
            $table->integer('stock')->default(0)->change();
            $table->index(['category_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'supplier_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['sku', 'category_id', 'supplier_id', 'cost']);
        });
    }
};
