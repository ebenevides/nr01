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
        Schema::create('evidencias', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('empresa_id');
            $table->uuid('plano_acao_id');
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->foreign('plano_acao_id')->references('id')->on('planos_acao')->cascadeOnDelete();
            $table->string('nome_arquivo');
            $table->string('caminho_s3');
            $table->string('tipo_mime', 100);
            $table->unsignedBigInteger('tamanho_bytes');
            $table->string('descricao')->nullable();
            $table->timestamps();
        });

        RlsHelper::enable('evidencias');
    }

    public function down(): void
    {
        RlsHelper::disable('evidencias');
        Schema::dropIfExists('evidencias');
    }
};
