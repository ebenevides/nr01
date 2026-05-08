<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

const props = defineProps({ empresa: Object })

const form = useForm({
    razao_social: props.empresa?.razao_social ?? '',
    cnpj: props.empresa?.cnpj ?? '',
    cnae: props.empresa?.cnae ?? '',
    grau_risco: props.empresa?.grau_risco ?? 1,
    responsavel_tecnico: props.empresa?.responsavel_tecnico ?? '',
    ativo: props.empresa?.ativo ?? true,
})

const submit = () => {
    if (props.empresa) {
        form.put(route('empresas.update', props.empresa.id))
    } else {
        form.post(route('empresas.store'))
    }
}
</script>

<template>
    <Head :title="empresa ? 'Editar Empresa' : 'Nova Empresa'" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ empresa ? 'Editar Empresa' : 'Nova Empresa' }}
                </h2>
                <Link :href="route('empresas.index')" class="text-sm text-gray-600 hover:text-gray-900">← Voltar</Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Razão Social *</label>
                            <input v-model="form.razao_social" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                :class="{ 'border-red-500': form.errors.razao_social }" />
                            <p v-if="form.errors.razao_social" class="mt-1 text-xs text-red-600">{{ form.errors.razao_social }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">CNPJ *</label>
                            <input v-model="form.cnpj" type="text" placeholder="00.000.000/0000-00"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                :class="{ 'border-red-500': form.errors.cnpj }" />
                            <p v-if="form.errors.cnpj" class="mt-1 text-xs text-red-600">{{ form.errors.cnpj }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">CNAE</label>
                                <input v-model="form.cnae" type="text" placeholder="ex: 4711-3/01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Grau de Risco *</label>
                                <select v-model.number="form.grau_risco"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500': form.errors.grau_risco }">
                                    <option :value="1">GR 1 — Baixo</option>
                                    <option :value="2">GR 2 — Médio</option>
                                    <option :value="3">GR 3 — Alto</option>
                                    <option :value="4">GR 4 — Muito Alto</option>
                                </select>
                                <p v-if="form.errors.grau_risco" class="mt-1 text-xs text-red-600">{{ form.errors.grau_risco }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Responsável Técnico</label>
                            <input v-model="form.responsavel_tecnico" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        </div>

                        <div class="flex items-center gap-2">
                            <input v-model="form.ativo" type="checkbox" id="ativo"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600" />
                            <label for="ativo" class="text-sm font-medium text-gray-700">Empresa ativa</label>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <Link :href="route('empresas.index')"
                                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                                {{ empresa ? 'Salvar' : 'Criar Empresa' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
