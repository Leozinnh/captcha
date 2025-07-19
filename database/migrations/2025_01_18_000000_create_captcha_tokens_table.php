<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('captcha_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do site/projeto
            $table->string('token', 64)->unique(); // Token único
            $table->string('domain')->nullable(); // Domínio autorizado
            $table->json('allowed_types')->nullable(); // Tipos de captcha permitidos
            $table->integer('daily_limit')->default(1000); // Limite diário
            $table->integer('usage_count')->default(0); // Contador de uso
            $table->date('last_used_at')->nullable(); // Última utilização
            $table->boolean('is_active')->default(true); // Status ativo/inativo
            $table->text('description')->nullable(); // Descrição
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('captcha_tokens');
    }
};