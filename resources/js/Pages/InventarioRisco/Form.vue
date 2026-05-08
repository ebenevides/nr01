<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
    ghes: Array,
    estabelecimentos: Array,
})

const form = useForm({
    ghe_id: '',
    estabelecimento_id: '',
    data_avaliacao: new Date().toISOString().slice(0, 10),
    responsavel: '',
    observacoes: '',
})

const onGheChange = () => {
    const ghe = props.ghes.find(g => g.id === form.ghe_id)
    if (ghe) form.estabelecimento_id = ghe.estabelecimento_id
}

const submit = () => {
    form.post(route('inventarios.store'))
}
</script>

<template>
    <Head title="Novo Inventário de Risco" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Novo Inventário de Risco
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">GHE</label>
                            <select
                                v-model="form.ghe_id"
                                @change="onGheChange"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">Selecione...</option>
                                <option v-for="ghe in ghes" :key="ghe.id" :value="ghe.id">
                                    {{ ghe.nome }} — {{ ghe.estabelecimento?.nome }}
                                </option>
                            </select>
                            <p v-if="form.errors.ghe_id" class="mt-1 text-sm text-red-600">{{ form.errors.ghe_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estabelecimento</label>
                            <select
                                v-model="form.estabelecimento_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">Selecione...</option>
                                <option v-for="est in estabelecimentos" :key="est.id" :value="est.id">
                                    {{ est.nome }}
                                </option>
                            </select>
                            <p v-if="form.errors.estabelecimento_id" class="mt-1 text-sm text-red-600">{{ form.errors.estabelecimento_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data da Avaliação</label>
                            <input
                                type="date"
                                v-model="form.data_avaliacao"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <p v-if="form.errors.data_avaliacao" class="mt-1 text-sm text-red-600">{{ form.errors.data_avaliacao }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Responsável Técnico</label>
                            <input
                                type="text"
                                v-model="form.responsavel"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                            <p v-if="form.errors.responsavel" class="mt-1 text-sm text-red-600">{{ form.errors.responsavel }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Observações</label>
                            <textarea
                                v-model="form.observacoes"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>

                        <div class="flex justify-end gap-3">
                            <a :href="route('inventarios.index')" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </a>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Criar Inventário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
