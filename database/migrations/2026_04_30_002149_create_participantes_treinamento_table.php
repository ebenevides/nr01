<?php

use App\Support\RlsHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participantes_treinamento', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('empresa_id');
            $table->uuid('treinamento_id');
            $table->uuid('ghe_id')->nullable();
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->foreign('treinamento_id')->references('id')->on('treinamentos')->cascadeOnDelete();
            $table->foreign('ghe_id')->references('id')->on('ghes')->nullOnDelete();
            $table->string('nome');
            $table->string('cpf', 14)->nullable();
            $table->string('cargo')->nullable();
            // data_realizacao + validade_meses do treinamento pai
            $table->date('valido_ate');
            $table->timestamps();

            $table->unique(['treinamento_id', 'cpf']);
        });

        RlsHelper::enable('participantes_treinamento');
    }

    public function down(): void
    {
        RlsHelper::disable('participantes_treinamento');
        Schema::dropIfExists('participantes_treinamento');
    }
};
