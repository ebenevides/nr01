<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({ empresa: Object })
</script>

<template>
    <Head :title="empresa.razao_social" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ empresa.razao_social }}</h2>
                    <p class="text-sm text-gray-500">{{ empresa.cnpj }}</p>
                </div>
                <div class="flex gap-3">
                    <Link :href="route('empresas.edit', empresa.id)"
                        class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Editar
                    </Link>
                    <Link :href="route('empresas.index')" class="text-sm text-gray-600 hover:text-gray-900 self-center">← Voltar</Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

                <!-- Dados gerais -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 text-sm font-semibold uppercase text-gray-500">Dados Gerais</h3>
                    <dl class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div>
                            <dt class="text-xs text-gray-500">CNAE</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ empresa.cnae || '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Grau de Risco</dt>
                            <dd class="text-sm font-medium text-gray-900">GR {{ empresa.grau_risco }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Responsável Técnico</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ empresa.responsavel_tecnico || '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Status</dt>
                            <dd>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="empresa.ativo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                    {{ empresa.ativo ? 'Ativa' : 'Inativa' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Estabelecimentos -->
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-semibold uppercase text-gray-500">Estabelecimentos</h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">CNPJ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Cidade/UF</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Trabalhadores</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="est in empresa.estabelecimentos" :key="est.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ est.nome }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ est.cnpj_estabelecimento || '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ est.cidade }}/{{ est.uf }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ est.total_trabalhadores }}</td>
                            </tr>
                            <tr v-if="empresa.estabelecimentos.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum estabelecimento.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Usuários -->
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-semibold uppercase text-gray-500">Usuários</h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">E-mail</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Role</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="u in empresa.usuarios" :key="u.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ u.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ u.email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ u.role }}</td>
                            </tr>
                            <tr v-if="empresa.usuarios.length === 0">
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum usuário.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
