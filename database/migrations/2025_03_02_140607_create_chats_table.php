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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('thread_id')->nullable(); // OpenAI thread ID
            $table->string('status')->default('open'); // 'open', 'ai_closed', 'ai_trial_scheduled', 'closed', 'trial_scheduled'
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('student_grade')->nullable(); // For Iqra Virtual School
            $table->string('student_age')->nullable(); // For Quran Home Tutor
            $table->string('class_days')->nullable(); // For Quran Home Tutor
            $table->string('class_time')->nullable(); // For Quran Home Tutor & Tuition Services
            $table->date('start_date')->nullable(); // For Quran Home Tutor
            $table->string('subjects')->nullable(); // For Tuition Services
            $table->string('session')->nullable(); // For Iqra Virtual School
            $table->boolean('human_transfer_requested')->default(false);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
