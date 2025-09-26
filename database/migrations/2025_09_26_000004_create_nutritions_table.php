<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNutritionsTable extends Migration
{
    public function up()
    {
        Schema::create('nutritions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->onDelete('cascade');
            $table->float('height')->nullable(); // cm
            $table->float('weight')->nullable(); // kg
            $table->float('bmi')->nullable();
            $table->float('lower_limit_weight')->nullable();
            $table->float('weight_limit_weight')->nullable();
            $table->float('visceral_fat')->nullable();
            $table->float('body_fat_percent')->nullable();
            $table->text('notes_nutritionist')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nutritions');
    }
}
