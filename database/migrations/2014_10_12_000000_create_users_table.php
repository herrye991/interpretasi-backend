<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

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
            $table->text('photo')->default('https://interpretasi.id/assets/images/users/default.jpg');
            $table->enum('type', ['0', '1', '2', '3'])->default('0');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert([
            [
                'id' => '1',
                'name' => 'Hanny Novianty',
                'email' => 'hannynovianty08@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('testuser'),
                'set_password' => '0',
                'photo' => 'https://lh3.googleusercontent.com/a/AEdFTp7rSQSGBXSkojDEr3RPtMeLn-bWycFhAjSzRoFVSQ=s48-c-rp-br100',
                'type' => '0',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
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
        Schema::dropIfExists('users');
    }
}
