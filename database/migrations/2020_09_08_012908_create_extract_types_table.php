<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateExtractTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extract_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
        });

        DB::table('extract_types')
            ->insert([
                [
                    'id' => 1,
                    'type' => 'deposit'
                ],
                [
                    'id' => 2,
                    'type' => 'investiment'
                ],
                [
                    'id' => 3,
                    'type' => 'liquidation'
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
        Schema::dropIfExists('extract_types');
    }
}
