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
        Schema::create('ranking_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('time_block_id');
            $table->json('affected_talks');
            $table->string('alert_type')->default('ranking_permutation');
            $table->text('details')->nullable();
            $table->timestamps();

            $table->foreign('time_block_id')->references('id')->on('time_blocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking_alerts');
    }
};
