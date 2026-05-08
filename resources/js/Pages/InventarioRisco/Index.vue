<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    inventarios: Object,
})

const nivelClass = (nivel) => {
    if (nivel <= 2) return 'bg-green-100 text-green-800'
    if (nivel <= 4) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
}

const statusLabel = {
    rascunho: 'Rascunho',
    em_revisao: 'Em Revisão',
    aprovado: 'Aprovado',
}
</script>

<template>
    <Head title="Inventários de Risco" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Inventários de Risco
                </h2>
                <Link
                    :href="route('inventarios.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    Novo Inventário
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">GHE</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estabelecimento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Data Avaliação</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Versão</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="inv in inventarios.data" :key="inv.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ inv.ghe?.nome }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ inv.estabelecimento?.nome }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ inv.data_avaliacao }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium"
                                        :class="{
                                            'bg-gray-100 text-gray-600': inv.status === 'rascunho',
                                            'bg-yellow-100 text-yellow-700': inv.status === 'em_revisao',
                                            'bg-green-100 text-green-700': inv.status === 'aprovado',
                                        }"
                                    >
                                        {{ statusLabel[inv.status] }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">v{{ inv.versao }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <Link :href="route('inventarios.show', inv.id)" class="text-indigo-600 hover:text-indigo-900">
                                        Ver
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="inventarios.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Nenhum inventário cadastrado.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
