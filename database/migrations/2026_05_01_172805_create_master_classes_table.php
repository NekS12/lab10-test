<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('type_id')->constrained('creativity_types')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->date('date');
            $table->enum('start_time', ['09:00', '11:00', '13:00', '15:00']);
            $table->integer('max_participants');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['instructor_id', 'date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_classes');
    }
};
