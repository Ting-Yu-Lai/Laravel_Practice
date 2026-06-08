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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('菜單名稱');
            $table->text('description')->nullable()->comment('菜單描述');
            $table->decimal('price', 8, 2)->comment('菜單價格');
            $table->integer('stock')->default(0)->comment('菜單庫存');
            $table->boolean('is_available')->default(true)->comment('菜單是否可用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
