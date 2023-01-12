<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Categories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'News'],
            ['id' => 2, 'name' => 'Entertainment'],
            ['id' => 3, 'name' => 'Mom'],
            ['id' => 4, 'name' => 'Food & Travel'],
            ['id' => 5, 'name' => 'Tech & Sains'],
            ['id' => 6, 'name' => 'Automotive'],
            ['id' => 7, 'name' => 'Woman'],
            ['id' => 8, 'name' => 'Lifestyle']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){ 

    }
}
