<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
    metricas: Object,
    graficos: Object,
})

const chartNivel = ref(null)
const chartStatus = ref(null)

onMounted(() => {
    const nvelLabels = Object.keys(props.graficos.perigos_por_nivel)
    const nivelData = Object.values(props.graficos.perigos_por_nivel)
    const nivelColors = nvelLabels.map(l =>
        l === 'Aceitável' ? '#86efac' : l === 'Tolerável' ? '#fde047' : '#fca5a5'
    )

    new Chart(chartNivel.value, {
        type: 'doughnut',
        data: { labels: nvelLabels, datasets: [{ data: nivelData, backgroundColor: nivelColors, borderWidth: 1 }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
    })

    const statusLabels = { pendente: 'Pendente', em_andamento: 'Em Andamento', concluido: 'Concluído', cancelado: 'Cancelado' }
    const sKeys = Object.keys(props.graficos.acoes_por_status)
    const sData = Object.values(props.graficos.acoes_por_status)
    const sColors = sKeys.map(k =>
        k === 'concluido' ? '#86efac' : k === 'cancelado' ? '#d1d5db' : k === 'em_andamento' ? '#93c5fd' : '#fde047'
    )

    new Chart(chartStatus.value, {
        type: 'bar',
        data: {
            labels: sKeys.map(k => statusLabels[k] ?? k),
            datasets: [{ data: sData, backgroundColor: sColors, borderRadius: 4 }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
        },
    })
})

const pct = (v, t) => t > 0 ? Math.round((v / t) * 100) : 0
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard NR-01</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- KPIs -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <!-- Conformidade -->
                    <div class="bg-white rounded-lg shadow-sm p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase">% Conformidade</p>
                        <p class="mt-2 text-4xl font-bold" :class="metricas.percentual_conformidade >= 80 ? 'text-green-600' : 'text-amber-500'">
                            {{ metricas.percentual_conformidade }}%
                        </p>
                        <p class="mt-1 text-xs text-gray-400">{{ metricas.acoes_concluidas }}/{{ metricas.total_acoes }} ações concluídas</p>
                        <div class="mt-3 w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all"
                                :class="metricas.percentual_conformidade >= 80 ? 'bg-green-500' : 'bg-amber-400'"
                                :style="{ width: metricas.percentual_conformidade + '%' }" />
                        </div>
                    </div>

                    <!-- Ações vencidas -->
                    <div class="bg-white rounded-lg shadow-sm p-5" :class="{ 'border-l-4 border-red-500': metricas.acoes_vencidas > 0 }">
                        <p class="text-xs font-medium text-gray-500 uppercase">Ações Vencidas</p>
                        <p class="mt-2 text-4xl font-bold" :class="metricas.acoes_vencidas > 0 ? 'text-red-600' : 'text-gray-700'">
                            {{ metricas.acoes_vencidas }}
                        </p>
                        <Link :href="route('planos.index')" class="mt-2 block text-xs text-indigo-600 hover:underline">Ver planos →</Link>
                    </div>

                    <!-- Treinamentos vencendo -->
                    <div class="bg-white rounded-lg shadow-sm p-5" :class="{ 'border-l-4 border-amber-400': metricas.treinamentos_vencendo > 0 }">
                        <p class="text-xs font-medium text-gray-500 uppercase">Certificados (30d)</p>
                        <p class="mt-2 text-4xl font-bold" :class="metricas.treinamentos_vencendo > 0 ? 'text-amber-600' : 'text-gray-700'">
                            {{ metricas.treinamentos_vencendo }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">{{ metricas.treinamentos_vencidos }} já vencidos</p>
                        <Link :href="route('treinamentos.index')" class="mt-1 block text-xs text-indigo-600 hover:underline">Ver treinamentos →</Link>
                    </div>

                    <!-- Perigos registrados -->
                    <div class="bg-white rounded-lg shadow-sm p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase">Perigos Mapeados</p>
                        <p class="mt-2 text-4xl font-bold text-gray-700">{{ metricas.total_perigos }}</p>
                        <Link :href="route('inventarios.index')" class="mt-2 block text-xs text-indigo-600 hover:underline">Ver inventários →</Link>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="mb-4 text-sm font-semibold text-gray-700">Perigos por Nível de Risco</h3>
                        <canvas ref="chartNivel" height="200" />
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="mb-4 text-sm font-semibold text-gray-700">Planos de Ação por Status</h3>
                        <canvas ref="chartStatus" height="200" />
                    </div>
                </div>

                <!-- Links rápidos -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <Link :href="route('inventarios.create')" class="flex items-center gap-2 rounded-lg bg-indigo-50 border border-indigo-100 p-4 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
                        📋 Novo Inventário
                    </Link>
                    <Link :href="route('planos.index')" class="flex items-center gap-2 rounded-lg bg-green-50 border border-green-100 p-4 text-sm font-medium text-green-700 hover:bg-green-100">
                        ✅ Planos de Ação
                    </Link>
                    <Link :href="route('treinamentos.create')" class="flex items-center gap-2 rounded-lg bg-amber-50 border border-amber-100 p-4 text-sm font-medium text-amber-700 hover:bg-amber-100">
                        🎓 Novo Treinamento
                    </Link>
                    <Link :href="route('documentos.index')" class="flex items-center gap-2 rounded-lg bg-purple-50 border border-purple-100 p-4 text-sm font-medium text-purple-700 hover:bg-purple-100">
                        📄 Gerar PGR/LTCAT
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
