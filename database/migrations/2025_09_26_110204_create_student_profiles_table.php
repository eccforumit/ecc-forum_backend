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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            $table->string('profile_image_url')->nullable();
            $table->string('profile_image_public_id')->nullable();
            $table->string('school');
            $table->string('school_name')->nullable();
            $table->string('major');
            $table->string('school_year');
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('cv_file_url')->nullable();
            $table->string('cv_file_public_id')->nullable();
            $table->timestamps();

            $table->index(['school']);
            $table->index(['major']);
            $table->index(['school_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
