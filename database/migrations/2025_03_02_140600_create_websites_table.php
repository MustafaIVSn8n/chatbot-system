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
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('api_key')->nullable(); // OpenAI API Key
            $table->string('assistant_id')->nullable(); // OpenAI Assistant ID
            $table->string('model_name')->nullable(); // OpenAI Model Name
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('widget_color')->default('#3498db');
            $table->string('widget_position')->default('bottom-right');
            $table->string('website_type')->default('default'); // 'iqra_virtual_school', 'quran_home_tutor', 'tuition_services'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
