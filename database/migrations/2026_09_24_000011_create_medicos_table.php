<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('clinica_id')->nullable()->constrained('clinicas');
            $table->foreignId('especialidad_id')->nullable()->constrained('especialidades');
            $table->string('cedula_profesional')->nullable()->unique();
            $table->integer('anios_experiencia')->nullable();
            $table->string('consultorio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};
