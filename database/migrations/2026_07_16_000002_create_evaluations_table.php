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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('talk_id');
            $table->unsignedTinyInteger('rating');
            $table->text('liked_aspects')->nullable();
            $table->text('improvement_aspects')->nullable();
            $table->string('device_signature', 64);
            $table->timestamps();

            $table->foreign('talk_id')->references('id')->on('talks')->onDelete('cascade');
            
            // Composite unique index to prevent duplicate votes per device per talk
            $table->unique(['talk_id', 'device_signature'], 'unique_talk_device_evaluation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
