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
        Schema::create('documentos', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('empresa_id');
            $table->foreignId('gerado_por')->constrained('users')->restrictOnDelete();
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->enum('tipo', ['pgr', 'ltcat']);
            $table->unsignedSmallInteger('versao')->default(1);
            $table->enum('status', ['gerando', 'concluido', 'erro'])->default('gerando');
            $table->string('caminho_s3')->nullable();
            $table->text('erro_msg')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        RlsHelper::enable('documentos');
    }

    public function down(): void
    {
        RlsHelper::disable('documentos');
        Schema::dropIfExists('documentos');
    }
};
