<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'

defineProps({
    planos: Object,
    statuses: Array,
})

const statusClass = (status) => ({
    pendente: 'bg-gray-100 text-gray-700',
    em_andamento: 'bg-blue-100 text-blue-700',
    concluido: 'bg-green-100 text-green-700',
    cancelado: 'bg-red-100 text-red-700',
}[status] ?? 'bg-gray-100 text-gray-700')

const updateStatus = (plano, status) => {
    router.patch(route('planos.status', plano.id), { status })
}

const uploadForm = useForm({ arquivo: null, descricao: '' })
const activeUpload = ref(null)

const submitUpload = (plano) => {
    uploadForm.post(route('planos.evidencias.store', plano.id), {
        onSuccess: () => {
            uploadForm.reset()
            activeUpload.value = null
        },
    })
}
</script>

<template>
    <Head title="Planos de Ação" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Planos de Ação</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-4">
                <div v-for="plano in planos.data" :key="plano.id" class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ plano.descricao_acao }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Resp: {{ plano.responsavel }} ·
                                Prazo: {{ plano.prazo }} ·
                                {{ plano.perigo?.inventario_risco?.ghe?.nome }}
                            </p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span v-for="ev in plano.evidencias" :key="ev.id" class="inline-flex items-center gap-1 rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600">
                                    📎 {{ ev.nome_arquivo }}
                                    <button @click="router.delete(route('planos.evidencias.destroy', { plano: plano.id, evidencia: ev.id }))" class="text-red-400 hover:text-red-600 ml-1">×</button>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <span class="rounded-full px-2 py-1 text-xs font-medium" :class="statusClass(plano.status)">
                                {{ plano.status }}
                            </span>
                            <select
                                :value="plano.status"
                                @change="updateStatus(plano, $event.target.value)"
                                class="text-xs rounded border-gray-300"
                            >
                                <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                            </select>
                            <button
                                @click="activeUpload = activeUpload === plano.id ? null : plano.id"
                                class="text-xs text-indigo-600 hover:underline"
                            >
                                + Evidência
                            </button>
                        </div>
                    </div>

                    <!-- Upload form -->
                    <div v-if="activeUpload === plano.id" class="mt-4 border-t pt-4">
                        <form @submit.prevent="submitUpload(plano)" class="flex items-end gap-3">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700">Arquivo (PDF, imagem, doc — máx 20 MB)</label>
                                <input type="file" @change="uploadForm.arquivo = $event.target.files[0]" class="mt-1 block w-full text-sm" />
                                <p v-if="uploadForm.errors.arquivo" class="text-xs text-red-600">{{ uploadForm.errors.arquivo }}</p>
                            </div>
                            <div class="w-48">
                                <label class="block text-xs font-medium text-gray-700">Descrição</label>
                                <input type="text" v-model="uploadForm.descricao" class="mt-1 block w-full rounded border-gray-300 text-sm" />
                            </div>
                            <button type="submit" :disabled="uploadForm.processing" class="rounded bg-indigo-600 px-3 py-2 text-xs font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                                Enviar
                            </button>
                        </form>
                    </div>
                </div>

                <p v-if="planos.data.length === 0" class="text-center py-12 text-sm text-gray-500">
                    Nenhum plano de ação cadastrado.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
