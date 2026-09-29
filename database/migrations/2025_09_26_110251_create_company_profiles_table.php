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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('industry');
            $table->string('company_size')->nullable();
            $table->text('company_description')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('logo_public_id')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_first_name');
            $table->string('contact_last_name');
            $table->string('contact_phone');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index(['industry']);
            $table->index(['company_size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
