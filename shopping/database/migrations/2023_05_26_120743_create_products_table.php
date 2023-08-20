<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('description');
            $table->string('description_ar')->nullable();
            $table->unsignedBiginteger('subcatigory_id');
            $table->foreign('subcatigory_id')->references('id')->on('subcatigoryes')->onDelete('cascade');
            $table->string('image');
            $table->string('size');
            $table->float('price');
            $table->float('discount')->default(0);
            $table->unsignedBiginteger('rating')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
