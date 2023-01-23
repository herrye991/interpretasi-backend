<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CreateUsersTable extends Migration
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
            $table->string('name')->default('');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->default('');
            $table->enum('set_password', ['0', '1'])->default('0');
            $table->text('photo')->default('');
            $table->enum('type', ['0', '1', '2', '3'])->default('0');
            $table->rememberToken();
            $table->timestamps();
        });
        DB::table('users')->insert([
            'id' => '1',
            'name' => 'Herry Mandala',
            'email' => 'herrymandala1@gmail.com',
            'password' => Hash::make('herrymandala1@gmail.com'),
            'email_verified_at' => Carbon::now(),
            'set_password' => '0',
            'created_at' => Carbon::now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
