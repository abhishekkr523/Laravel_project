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
        Schema::table('products', function (Blueprint $table) {
            
            $table->unsignedBigInteger('product_condition_id')->nullable();
   
            $table->foreign('product_condition_id')
                  ->references('id')
                  ->on('product_conditions')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the foreign key constraint and the column
            $table->dropForeign(['product_condition_id']);
            $table->dropColumn('product_condition_id');
        });

    }
};
