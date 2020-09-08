<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestimentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->decimal('amount');
            $table->decimal('price', 10, 5);
            $table->dateTime('liquidated_at')->nullable()->default(NULL);
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
        Schema::dropIfExists('investiments');
    }
}
