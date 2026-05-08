<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

defineProps({
    tipos: Array,
    ghes: Array,
})

const form = useForm({
    titulo: '',
    tipo: 'outro',
    carga_horaria: 8,
    data_realizacao: new Date().toISOString().slice(0, 10),
    validade_meses: 12,
    instrutor: '',
    local: '',
    lista_presenca: null,
    observacoes: '',
})

const submit = () => {
    form.post(route('treinamentos.store'), {
        forceFormData: true,
    })
}
</script>

<template>
    <Head title="Novo Treinamento" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Novo Treinamento</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" v-model="form.titulo" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                            <p v-if="form.errors.titulo" class="mt-1 text-xs text-red-600">{{ form.errors.titulo }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select v-model="form.tipo" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm">
                                    <option v-for="t in tipos" :key="t.value" :value="t.value">{{ t.label }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Carga Horária (h)</label>
                                <input type="number" v-model="form.carga_horaria" min="1" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data de Realização</label>
                                <input type="date" v-model="form.data_realizacao" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Validade (meses)</label>
                                <input type="number" v-model="form.validade_meses" min="1" max="60" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Instrutor</label>
                                <input type="text" v-model="form.instrutor" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Local</label>
                                <input type="text" v-model="form.local" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lista de Presença (PDF, máx 20 MB)</label>
                            <input type="file" accept=".pdf" @change="form.lista_presenca = $event.target.files[0]" class="mt-1 block w-full text-sm" />
                            <p v-if="form.errors.lista_presenca" class="mt-1 text-xs text-red-600">{{ form.errors.lista_presenca }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Observações</label>
                            <textarea v-model="form.observacoes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                        </div>

                        <div class="flex justify-end gap-3">
                            <Link :href="route('treinamentos.index')" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancelar</Link>
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
