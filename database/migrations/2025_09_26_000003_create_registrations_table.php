<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('surname');
            $table->enum('sex', ['Male', 'Female', 'Other'])->nullable();
            $table->date('dob')->nullable();
            $table->integer('age')->nullable();

            // ✅ Unique phone & email
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('corporate_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registrations');
    }
};
