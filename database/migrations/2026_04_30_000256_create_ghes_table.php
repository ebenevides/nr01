<?php

use App\Support\RlsHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ghes', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('empresa_id');
            $table->uuid('estabelecimento_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->foreign('estabelecimento_id')->references('id')->on('estabelecimentos')->cascadeOnDelete();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->integer('num_trabalhadores')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        RlsHelper::enable('ghes');
    }

    public function down(): void
    {
        RlsHelper::disable('ghes');
        Schema::dropIfExists('ghes');
    }
};
