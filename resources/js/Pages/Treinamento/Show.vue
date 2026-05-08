<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    treinamento: Object,
    listaPresencaUrl: String,
    ghes: Array,
})

const showForm = ref(false)

const form = useForm({
    participantes: [{ nome: '', cpf: '', cargo: '', ghe_id: '' }],
})

const addLinha = () => form.participantes.push({ nome: '', cpf: '', cargo: '', ghe_id: '' })
const removerLinha = (i) => form.participantes.splice(i, 1)

const submit = () => {
    form.post(route('treinamentos.participantes.store', props.treinamento.id), {
        onSuccess: () => {
            showForm.value = false
            form.reset()
            form.participantes = [{ nome: '', cpf: '', cargo: '', ghe_id: '' }]
        },
    })
}

const removerParticipante = (p) => {
    if (confirm(`Remover ${p.nome}?`)) {
        router.delete(route('treinamentos.participantes.destroy', { treinamento: props.treinamento.id, participante: p.id }))
    }
}
</script>

<template>
    <Head :title="`Treinamento — ${treinamento.titulo}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ treinamento.titulo }}</h2>
                    <p class="text-sm text-gray-500">
                        {{ treinamento.instrutor }} · {{ treinamento.data_realizacao }} ·
                        {{ treinamento.carga_horaria }}h · validade {{ treinamento.validade_meses }} meses
                    </p>
                </div>
                <div class="flex gap-3">
                    <a v-if="listaPresencaUrl" :href="listaPresencaUrl" target="_blank" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        📎 Lista de Presença
                    </a>
                    <button @click="showForm = !showForm" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        + Participantes
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- Formulário de participantes -->
                <div v-if="showForm" class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 text-base font-medium text-gray-900">Adicionar Participantes</h3>
                    <form @submit.prevent="submit" class="space-y-3">
                        <div v-for="(p, i) in form.participantes" :key="i" class="grid grid-cols-12 gap-2 items-end">
                            <div class="col-span-3">
                                <label v-if="i === 0" class="block text-xs font-medium text-gray-700">Nome *</label>
                                <input type="text" v-model="p.nome" placeholder="Nome" class="block w-full rounded border-gray-300 text-sm" required />
                            </div>
                            <div class="col-span-2">
                                <label v-if="i === 0" class="block text-xs font-medium text-gray-700">CPF</label>
                                <input type="text" v-model="p.cpf" placeholder="000.000.000-00" class="block w-full rounded border-gray-300 text-sm" />
                            </div>
                            <div class="col-span-2">
                                <label v-if="i === 0" class="block text-xs font-medium text-gray-700">Cargo</label>
                                <input type="text" v-model="p.cargo" placeholder="Cargo" class="block w-full rounded border-gray-300 text-sm" />
                            </div>
                            <div class="col-span-3">
                                <label v-if="i === 0" class="block text-xs font-medium text-gray-700">GHE</label>
                                <select v-model="p.ghe_id" class="block w-full rounded border-gray-300 text-sm">
                                    <option value="">— GHE —</option>
                                    <option v-for="g in ghes" :key="g.id" :value="g.id">{{ g.nome }}</option>
                                </select>
                            </div>
                            <div class="col-span-2 flex gap-2">
                                <button type="button" @click="addLinha" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50">+</button>
                                <button type="button" @click="removerLinha(i)" v-if="form.participantes.length > 1" class="rounded border border-red-300 px-2 py-1 text-xs text-red-600 hover:bg-red-50">×</button>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showForm = false" class="rounded border border-gray-300 px-4 py-2 text-sm text-gray-700">Cancelar</button>
                            <button type="submit" :disabled="form.processing" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">Salvar</button>
                        </div>
                    </form>
                </div>

                <!-- Lista de participantes -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">CPF</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Cargo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">GHE</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Válido até</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="p in treinamento.participantes" :key="p.id"
                                :class="{ 'bg-red-50': p.valido_ate < new Date().toISOString().slice(0,10) }"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ p.nome }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ p.cpf ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ p.cargo ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ p.ghe?.nome ?? '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ p.valido_ate }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <button @click="removerParticipante(p)" class="text-red-600 hover:text-red-900">Remover</button>
                                </td>
                            </tr>
                            <tr v-if="treinamento.participantes.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum participante registrado.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
