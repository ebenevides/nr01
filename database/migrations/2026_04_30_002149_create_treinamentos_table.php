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
        Schema::create('treinamentos', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->string('titulo');
            $table->enum('tipo', ['integracao', 'nr01', 'primeiros_socorros', 'uso_epi', 'outro'])->default('outro');
            $table->unsignedSmallInteger('carga_horaria');
            $table->date('data_realizacao');
            $table->unsignedSmallInteger('validade_meses')->default(12);
            $table->string('instrutor');
            $table->string('local')->nullable();
            $table->string('lista_presenca_s3')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        RlsHelper::enable('treinamentos');
    }

    public function down(): void
    {
        RlsHelper::disable('treinamentos');
        Schema::dropIfExists('treinamentos');
    }
};
