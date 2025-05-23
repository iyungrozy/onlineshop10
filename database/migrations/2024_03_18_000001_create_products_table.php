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
            $table->string('name');
            $table->string('brand');
            $table->string('category');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_url')->nullable();
            $table->string('digiflazz_id')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Add indexes for frequently queried columns
            $table->index('name');
            $table->index('category');
            $table->index('brand');
            $table->index('active');
            $table->index('digiflazz_id');
            $table->index(['name', 'brand', 'category']);
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
