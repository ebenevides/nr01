<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({ empresas: Object })
</script>

<template>
    <Head title="Empresas" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Empresas</h2>
                <Link :href="route('empresas.create')" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Nova Empresa
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Razão Social</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">CNPJ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Grau Risco</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Estabelecimentos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="e in empresas.data" :key="e.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ e.razao_social }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ e.cnpj }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">GR {{ e.grau_risco }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ e.estabelecimentos_count }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="e.ativo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                        {{ e.ativo ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <Link :href="route('empresas.show', e.id)" class="text-indigo-600 hover:text-indigo-900">Ver</Link>
                                    <Link :href="route('empresas.edit', e.id)" class="text-gray-600 hover:text-gray-900">Editar</Link>
                                </td>
                            </tr>
                            <tr v-if="empresas.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Nenhuma empresa cadastrada.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
