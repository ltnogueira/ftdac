<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cadastros', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nome');
            $table->string('apelido')->nullable();
            $table->string('cep', 8)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento')->nullable();
            $table->string('celular', 13);
            $table->string('email')->nullable();
            $table->string('lideranca');
            $table->string('atualizado_por');
            $table->enum('tipo_contato', ['visita', 'ligacao']);
            $table->timestamps();

            $table->index(['nome', 'celular']);
            $table->index(['lideranca', 'tipo_contato']);
            $table->index('atualizado_por');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cadastros');
    }
};
