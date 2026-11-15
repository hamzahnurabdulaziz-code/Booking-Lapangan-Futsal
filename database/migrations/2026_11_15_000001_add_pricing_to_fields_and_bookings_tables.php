<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->unsignedInteger('price_per_hour')->default(150000)->after('description');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedInteger('total_price')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn('price_per_hour');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('total_price');
        });
    }
};
