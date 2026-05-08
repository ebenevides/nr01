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
        Schema::create('inventarios_risco', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('empresa_id');
            $table->uuid('ghe_id');
            $table->uuid('estabelecimento_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->foreign('ghe_id')->references('id')->on('ghes')->cascadeOnDelete();
            $table->foreign('estabelecimento_id')->references('id')->on('estabelecimentos')->cascadeOnDelete();
            $table->date('data_avaliacao');
            $table->string('responsavel');
            $table->enum('status', ['rascunho', 'em_revisao', 'aprovado'])->default('rascunho');
            $table->unsignedSmallInteger('versao')->default(1);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        RlsHelper::enable('inventarios_risco');
    }

    public function down(): void
    {
        RlsHelper::disable('inventarios_risco');
        Schema::dropIfExists('inventarios_risco');
    }
};
