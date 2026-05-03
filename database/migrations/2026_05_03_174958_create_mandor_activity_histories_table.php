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
        Schema::create('mandor_activity_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandor_id')->constrained('mandors')->onDelete('cascade');
            $table->enum('activity_type', [
                'assigned_project',
                'completed_project', 
                'offer_submitted',
                'negotiation_received',
                'offer_accepted',
                'renovation_completed'
            ]);
            $table->enum('reference_type', ['proyek', 'request_renovasi']);
            $table->unsignedBigInteger('reference_id');
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['mandor_id', 'created_at']);
            $table->index(['activity_type', 'reference_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mandor_activity_histories');
    }
};
