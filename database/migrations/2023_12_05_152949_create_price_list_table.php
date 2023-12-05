<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('price_list', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->text('name')->nullable();
            $table->string('bottle_size')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('price_gbp', 8, 2);
            $table->integer('order_amount')->default(0);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_list');
    }
};
