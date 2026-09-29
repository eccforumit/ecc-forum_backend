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
        Schema::create('cv_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained('student_profiles')->cascadeOnDelete();
            $table->string('title');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->text('summary')->nullable();
            $table->json('skills')->nullable();
            $table->json('work_experiences')->nullable();
            $table->json('educations')->nullable();
            $table->json('languages')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->string('twitter')->nullable();
            $table->string('profile_slug')->unique();
            $table->string('profile_url')->nullable();
            $table->string('qr_code_url')->nullable();
            $table->timestamps();

            $table->index(['profile_slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_documents');
    }
};
