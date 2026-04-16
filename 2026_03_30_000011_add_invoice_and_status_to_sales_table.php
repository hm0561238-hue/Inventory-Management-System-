<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->unique()->after('id');
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('completed')->after('sold_at');
            $table->text('notes')->nullable()->after('status');
            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['invoice_number']);
            $table->dropColumn(['invoice_number', 'status', 'notes']);
        });
    }
};
