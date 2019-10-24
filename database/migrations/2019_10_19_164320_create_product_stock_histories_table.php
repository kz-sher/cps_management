<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStockHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('person_involved');
            $table->string('stock_status');
            $table->string('old_prod_name');
            $table->string('new_prod_name');
            $table->string('stock_amount_status');
            $table->integer('original_stock_amount');
            $table->integer('update_stock_amount');
            $table->integer('curr_stock_amount');
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
        Schema::dropIfExists('product_stock_histories');
    }
}
