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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unique_name');
            $table->string('color');
            $table->string('image');
        });
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Kesehatan', 'unique_name' => 'kesehatan','color' => '#ffae25', 'image' => ''],
            ['id' => 2, 'name' => 'Berita', 'unique_name' => 'berita','color' => '#ffae25', 'image' => ''],
            ['id' => 3, 'name' => 'Mancanegara', 'unique_name' => 'mancanegara','color' => '#ffae25', 'image' => ''],
            ['id' => 4, 'name' => 'Ekonomi & Bisnis', 'unique_name' => 'ekonomi-bisnis','color' => '#ffae25', 'image' => ''],
            ['id' => 5, 'name' => 'Politik', 'unique_name' => 'politik','color' => '#ffae25', 'image' => ''],
            ['id' => 6, 'name' => 'Wisata & Kuliner', 'unique_name' => 'wisata-kuliner','color' => '#ffae25', 'image' => ''],
            ['id' => 7, 'name' => 'Games & Esports', 'unique_name' => 'games-esports','color' => '#ffae25', 'image' => ''],
            ['id' => 8, 'name' => 'Olahraga', 'unique_name' => 'olahraga','color' => '#ffae25', 'image' => ''],
            ['id' => 9, 'name' => 'Sejarah', 'unique_name' => 'sejarah','color' => '#ffae25', 'image' => ''],
            ['id' => 10, 'name' => 'Teknologi', 'unique_name' => 'teknologi','color' => '#ffae25', 'image' => ''],
            ['id' => 11, 'name' => 'Karir & Info Loker', 'unique_name' => 'karir-info-loker','color' => '#ffae25', 'image' => ''],
            ['id' => 12, 'name' => 'Entertainment', 'unique_name' => 'entertainment','color' => '#ffae25', 'image' => ''],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){ 
        Schema::dropIfExists('categories');
    }
}
