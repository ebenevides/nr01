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
        Schema::create('perigos', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('empresa_id');
            $table->uuid('inventario_risco_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->foreign('inventario_risco_id')->references('id')->on('inventarios_risco')->cascadeOnDelete();
            $table->enum('grupo_risco', ['fisico', 'quimico', 'biologico', 'ergonomico', 'acidente']);
            $table->text('descricao_perigo');
            $table->text('fonte_geradora')->nullable();
            $table->integer('trabalhadores_expostos')->default(0);
            // 1=baixa, 2=media, 3=alta
            $table->unsignedTinyInteger('probabilidade')->default(1);
            // 1=leve, 2=moderado, 3=grave
            $table->unsignedTinyInteger('severidade')->default(1);
            // nivel = probabilidade * severidade (1-9)
            $table->unsignedTinyInteger('nivel_risco')->storedAs('probabilidade * severidade');
            $table->text('medidas_existentes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        RlsHelper::enable('perigos');
    }

    public function down(): void
    {
        RlsHelper::disable('perigos');
        Schema::dropIfExists('perigos');
    }
};
