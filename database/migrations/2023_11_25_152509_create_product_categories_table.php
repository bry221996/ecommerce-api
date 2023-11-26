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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('merchant_id')
                ->references('id')
                ->on('merchants')
                ->cascadeOnDelete();

            $table->foreign('parent_id')
                ->references('id')
                ->on('product_categories')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
