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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('list_product_category_id')->constrained('list_products_categories', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name', 255);
            $table->double('price', 10, 2);
            $table->string('description', 255);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
