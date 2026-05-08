<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    inventario: Object,
    gruposRisco: Array,
})

const showForm = ref(false)
const editingPerigo = ref(null)

const form = useForm({
    grupo_risco: '',
    descricao_perigo: '',
    fonte_geradora: '',
    trabalhadores_expostos: 0,
    probabilidade: 1,
    severidade: 1,
    medidas_existentes: '',
})

const nivelLabel = (nivel) => {
    if (nivel <= 2) return 'Aceitável'
    if (nivel <= 4) return 'Tolerável'
    return 'Inaceitável'
}

const nivelClass = (nivel) => {
    if (nivel <= 2) return 'bg-green-100 text-green-800'
    if (nivel <= 4) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
}

const openEdit = (perigo) => {
    editingPerigo.value = perigo
    form.grupo_risco = perigo.grupo_risco
    form.descricao_perigo = perigo.descricao_perigo
    form.fonte_geradora = perigo.fonte_geradora ?? ''
    form.trabalhadores_expostos = perigo.trabalhadores_expostos
    form.probabilidade = perigo.probabilidade
    form.severidade = perigo.severidade
    form.medidas_existentes = perigo.medidas_existentes ?? ''
    showForm.value = true
}

const cancelForm = () => {
    showForm.value = false
    editingPerigo.value = null
    form.reset()
}

const submitPerigo = () => {
    if (editingPerigo.value) {
        form.put(
            route('inventarios.perigos.update', { inventario: props.inventario.id, perigo: editingPerigo.value.id }),
            { onSuccess: cancelForm }
        )
    } else {
        form.post(
            route('inventarios.perigos.store', props.inventario.id),
            { onSuccess: cancelForm }
        )
    }
}

const deletePerigo = (perigo) => {
    if (confirm('Remover este perigo?')) {
        router.delete(route('inventarios.perigos.destroy', { inventario: props.inventario.id, perigo: perigo.id }))
    }
}
</script>

<template>
    <Head :title="`Inventário — ${inventario.ghe?.nome}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Inventário de Risco</h2>
                    <p class="text-sm text-gray-500">{{ inventario.ghe?.nome }} · {{ inventario.estabelecimento?.nome }} · {{ inventario.data_avaliacao }}</p>
                </div>
                <button
                    @click="showForm = true"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Adicionar Perigo
                </button>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- Formulário perigo -->
                <div v-if="showForm" class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 text-base font-medium text-gray-900">
                        {{ editingPerigo ? 'Editar Perigo' : 'Novo Perigo' }}
                    </h3>
                    <form @submit.prevent="submitPerigo" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Grupo de Risco</label>
                            <select v-model="form.grupo_risco" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm">
                                <option value="">Selecione...</option>
                                <option v-for="g in gruposRisco" :key="g.value" :value="g.value">{{ g.label }}</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Descrição do Perigo</label>
                            <textarea v-model="form.descricao_perigo" rows="2" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fonte Geradora</label>
                            <input type="text" v-model="form.fonte_geradora" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Trabalhadores Expostos</label>
                            <input type="number" v-model="form.trabalhadores_expostos" min="0" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Probabilidade (1-3)</label>
                            <select v-model="form.probabilidade" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm">
                                <option :value="1">1 — Baixa</option>
                                <option :value="2">2 — Média</option>
                                <option :value="3">3 — Alta</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Severidade (1-3)</label>
                            <select v-model="form.severidade" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm">
                                <option :value="1">1 — Leve</option>
                                <option :value="2">2 — Moderado</option>
                                <option :value="3">3 — Grave</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Medidas de Controle Existentes</label>
                            <textarea v-model="form.medidas_existentes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 sm:text-sm" />
                        </div>

                        <div class="sm:col-span-2 flex justify-end gap-3">
                            <button type="button" @click="cancelForm" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Lista de perigos -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Grupo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Perigo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">P × S</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nível</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Expostos</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="perigo in inventario.perigos" :key="perigo.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 capitalize">
                                    {{ perigo.grupo_risco }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                                    {{ perigo.descricao_perigo }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ perigo.probabilidade }} × {{ perigo.severidade }} = {{ perigo.nivel_risco }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium" :class="nivelClass(perigo.nivel_risco)">
                                        {{ nivelLabel(perigo.nivel_risco) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ perigo.trabalhadores_expostos }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm space-x-3">
                                    <button @click="openEdit(perigo)" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                    <button @click="deletePerigo(perigo)" class="text-red-600 hover:text-red-900">Remover</button>
                                </td>
                            </tr>
                            <tr v-if="inventario.perigos.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Nenhum perigo registrado. Clique em "+ Adicionar Perigo".
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
