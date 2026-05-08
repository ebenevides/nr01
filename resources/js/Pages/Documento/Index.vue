<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

defineProps({
    documentos: Object,
    tipos: Array,
})

const form = useForm({ tipo: 'pgr' })

const gerar = () => {
    form.post(route('documentos.store'))
}

const statusClass = (status) => ({
    gerando: 'bg-blue-100 text-blue-700',
    concluido: 'bg-green-100 text-green-700',
    erro: 'bg-red-100 text-red-700',
}[status])

const statusLabel = { gerando: 'Gerando…', concluido: 'Pronto', erro: 'Erro' }
</script>

<template>
    <Head title="Documentos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Documentos Legais</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-6">

                <!-- Gerar novo -->
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 text-base font-medium text-gray-900">Gerar Documento</h3>
                    <form @submit.prevent="gerar" class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select v-model="form.tipo" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm">
                                <option v-for="t in tipos" :key="t.value" :value="t.value">{{ t.label }}</option>
                            </select>
                        </div>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                            Gerar PDF
                        </button>
                    </form>
                    <p class="mt-2 text-xs text-gray-500">Geração assíncrona via fila. Recarregue a página para ver o status.</p>
                </div>

                <!-- Histórico -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Versão</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Gerado por</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Data</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="doc in documentos.data" :key="doc.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 uppercase">{{ doc.tipo }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">v{{ doc.versao }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium" :class="statusClass(doc.status)">
                                        {{ statusLabel[doc.status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ doc.gerador?.name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ doc.created_at?.slice(0, 10) }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a v-if="doc.status === 'concluido'"
                                       :href="route('documentos.download', doc.id)"
                                       class="text-indigo-600 hover:text-indigo-900">
                                        Download
                                    </a>
                                    <span v-else-if="doc.status === 'erro'" class="text-red-500 text-xs" :title="doc.erro_msg">Falhou</span>
                                </td>
                            </tr>
                            <tr v-if="documentos.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum documento gerado.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
