<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    treinamentos: Object,
    vencendo: Array,
})
</script>

<template>
    <Head title="Treinamentos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Treinamentos</h2>
                <Link :href="route('treinamentos.create')" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Novo Treinamento
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- Alerta vencendo em 30 dias -->
                <div v-if="vencendo.length > 0" class="rounded-lg bg-amber-50 border border-amber-200 p-4">
                    <p class="text-sm font-medium text-amber-800">⚠ {{ vencendo.length }} certificado(s) vencendo em 30 dias:</p>
                    <ul class="mt-2 space-y-1">
                        <li v-for="p in vencendo" :key="p.id" class="text-sm text-amber-700">
                            {{ p.nome }} — {{ p.treinamento?.titulo }} — vence {{ p.valido_ate }}
                        </li>
                    </ul>
                </div>

                <!-- Tabela de treinamentos -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Realização</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Validade</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Participantes</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="t in treinamentos.data" :key="t.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ t.titulo }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ t.tipo }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ t.data_realizacao }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ t.validade_meses }} meses</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ t.participantes_count }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <Link :href="route('treinamentos.show', t.id)" class="text-indigo-600 hover:text-indigo-900">Ver</Link>
                                </td>
                            </tr>
                            <tr v-if="treinamentos.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum treinamento registrado.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
