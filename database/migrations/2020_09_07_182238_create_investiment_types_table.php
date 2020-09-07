<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateInvestimentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investiment_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
        });

        DB::table('investiment_types')
            ->insert([
                [
                    'id' => 1,
                    'type' => 'buy'
                ],
                [
                    'id' => 2,
                    'type' => 'sell'
                ]
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investiment_types');
    }
}
