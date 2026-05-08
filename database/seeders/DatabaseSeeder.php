<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Estabelecimento;
use App\Models\Ghe;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // RLS desabilitado durante seed — superuser ignora policies
        DB::statement('SET session_replication_role = replica');

        $empresa = Empresa::create([
            'id' => Str::uuid(),
            'razao_social' => 'Empresa Demo Ltda',
            'cnpj' => '12.345.678/0001-90',
            'cnae' => '4711-3/01',
            'grau_risco' => 3,
            'responsavel_tecnico' => 'Dr. João Silva',
            'ativo' => true,
        ]);

        $admin = User::create([
            'id' => Str::uuid(),
            'name' => 'Admin Demo',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'empresa_id' => $empresa->id,
            'role' => 'admin_empresa',
            'email_verified_at' => now(),
        ]);

        User::create([
            'id' => Str::uuid(),
            'name' => 'Técnico Segurança',
            'email' => 'tecnico@demo.com',
            'password' => Hash::make('password'),
            'empresa_id' => $empresa->id,
            'role' => 'tecnico_seguranca',
            'email_verified_at' => now(),
        ]);

        // Set tenant para permitir criação das entidades dependentes
        DB::statement("SET app.tenant_id = '{$empresa->id}'");

        $est = Estabelecimento::create([
            'id' => Str::uuid(),
            'empresa_id' => $empresa->id,
            'nome' => 'Sede São Paulo',
            'cnpj_estabelecimento' => '12.345.678/0001-90',
            'endereco' => 'Av. Paulista, 1000',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
            'cep' => '01310-100',
            'total_trabalhadores' => 85,
        ]);

        Ghe::create([
            'id' => Str::uuid(),
            'empresa_id' => $empresa->id,
            'estabelecimento_id' => $est->id,
            'nome' => 'Operadores de Máquinas',
            'descricao' => 'Trabalhadores que operam prensas e tornos mecânicos',
            'total_trabalhadores' => 30,
        ]);

        Ghe::create([
            'id' => Str::uuid(),
            'empresa_id' => $empresa->id,
            'estabelecimento_id' => $est->id,
            'nome' => 'Administrativo',
            'descricao' => 'Colaboradores de escritório',
            'total_trabalhadores' => 55,
        ]);

        DB::statement('SET session_replication_role = DEFAULT');

        $this->command->info("Seed OK — login: admin@demo.com / password");
    }
}
