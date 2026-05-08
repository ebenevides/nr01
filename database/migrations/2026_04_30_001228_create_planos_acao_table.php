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
        Schema::create('planos_acao', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('empresa_id');
            $table->uuid('perigo_id');
            $table->uuid('inventario_risco_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->foreign('perigo_id')->references('id')->on('perigos')->cascadeOnDelete();
            $table->foreign('inventario_risco_id')->references('id')->on('inventarios_risco')->cascadeOnDelete();
            $table->text('descricao_acao');
            $table->enum('tipo_controle', [
                'eliminacao',
                'substituicao',
                'controle_engenharia',
                'controle_administrativo',
                'epi',
            ])->default('controle_administrativo');
            $table->string('responsavel');
            $table->date('prazo');
            $table->enum('status', ['pendente', 'em_andamento', 'concluido', 'cancelado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamp('concluido_em')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        RlsHelper::enable('planos_acao');
    }

    public function down(): void
    {
        RlsHelper::disable('planos_acao');
        Schema::dropIfExists('planos_acao');
    }
};
