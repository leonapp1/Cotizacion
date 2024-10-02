<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import * as XLSX from 'xlsx';

const props = defineProps({ cotizaciones: Array });

const currentPage = ref(1);
const itemsPerPage = 10;
const searchQuery = ref('');

const filteredCotizaciones = computed(() => {
    return props.cotizaciones.filter(cotizacion => {
        return (
            cotizacion.cliente?.dni.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            cotizacion.cliente?.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            cotizacion.cliente?.apellido.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            cotizacion.id.toString().includes(searchQuery.value)
        );
    }).sort((a, b) => (b.total - b.descuento) - (a.total - a.descuento));
});

const paginatedCotizaciones = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredCotizaciones.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredCotizaciones.value.length / itemsPerPage);
});

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const downloadExcel = () => {
    const ws = XLSX.utils.json_to_sheet(filteredCotizaciones.value);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Cotizaciones");
    XLSX.writeFile(wb, "cotizaciones.xlsx");
};

</script>

<template>
    <Head title="Detalles Cotizacion" />
    <AuthenticatedLayout>
        <template #header>
            Detalles Cotizacion
        </template>

        <div class="p-4 bg-white rounded-lg shadow-xs">
            <input v-model="searchQuery" type="text" placeholder="Buscar..." class="mb-4 px-4 py-2 border rounded" />
            <button @click="downloadExcel" class="mb-4 px-4 py-2 text-white bg-green-500 rounded">Descargar Excel</button>
            <table class="w-full striped sm:border sm:border-slate-200 sm:dark:border-slate-800 dark:border-slate-700">

                <thead class="hidden border-0 sm:table-header-group">
                    <tr>
                        <th
                            class="bg-slate-100 px-4 py-2 dark:bg-slate-800 border-0 border-b border-slate-100 dark:border-slate-700 uppercase font-medium text-slate-600 dark:text-slate-400 text-left text-xs sm:pl-6">
                            #</th>
                        <th
                            class="bg-slate-100 px-4 py-2 dark:bg-slate-800 border-0 border-b border-slate-100 dark:border-slate-700 uppercase font-medium text-slate-600 dark:text-slate-400 text-left text-xs">
                            Dni Cliente</th>
                        <th
                            class="bg-slate-100 px-4 py-2 dark:bg-slate-800 border-0 border-b border-slate-100 dark:border-slate-700 uppercase font-medium text-slate-600 dark:text-slate-400 text-left text-xs">
                            Nombr Cliente</th>
                        <th
                            class="bg-slate-100 px-4 py-2 dark:bg-slate-800 border-0 border-b border-slate-100 dark:border-slate-700 uppercase font-medium text-slate-600 dark:text-slate-400 text-left text-xs">
                            Sub Total</th>
                        <th
                            class="bg-slate-100 px-4 py-2 dark:bg-slate-800 border-0 border-b border-slate-100 dark:border-slate-700 uppercase font-medium text-slate-600 dark:text-slate-400 text-left text-xs sm:pr-6">
                            Descuento
                        </th>
                        <th
                            class="bg-slate-100 px-4 py-2 dark:bg-slate-800 border-0 border-b border-slate-100 dark:border-slate-700 uppercase font-medium text-slate-600 dark:text-slate-400 text-left text-xs sm:pr-6">
                            Total
                        </th>
                        <th
                            class="bg-slate-100 px-4 py-2 dark:bg-slate-800 border-0 border-b border-slate-100 dark:border-slate-700 uppercase font-medium text-slate-600 dark:text-slate-400 text-left text-xs sm:pr-6">
                            <span class="sr-only"></span>
                        </th>
                        <th
                            class="bg-slate-100 px-4 py-2 dark:bg-slate-800 border-0 border-b border-slate-100 dark:border-slate-700 uppercase font-medium text-slate-600 dark:text-slate-400 text-left text-xs sm:pr-6">
                            <span class="sr-only"></span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-transparent">
                    <tr class="text-sm border border-slate-200 flex flex-col mb-6 py-1 divide-y divide-y-slate-50 dark:border-slate-800 sm:border-0 sm:table-row sm:mb-0 sm:py-0 dark:divide-slate-800 sm:divide-none"
                        v-for="a, i in paginatedCotizaciones" :key="a.id">
                        <td class="flex flex-col px-4 py-2 sm:table-cell sm:py-4 before:content-[attr(data-label)] sm:before:content-none before:text-[0.625rem] before:uppercase before:font-medium sm:pl-6"
                            data-label="#">{{ a.id }}</td>
                        <td class="flex flex-col px-4 py-2 sm:table-cell sm:py-4 before:content-[attr(data-label)] sm:before:content-none before:text-[0.625rem] before:uppercase before:font-medium"
                            data-label="Dni Cliente">{{ a.cliente?.dni }}</td>
                        <td class="flex flex-col px-4 py-2 sm:table-cell sm:py-4 before:content-[attr(data-label)] sm:before:content-none before:text-[0.625rem] before:uppercase before:font-medium"
                            data-label="Nombre cliente">{{ a.cliente?.nombre }} {{ a.cliente?.apellido }}</td>
                        <td class="flex flex-col px-4 py-2 sm:table-cell sm:py-4 before:content-[attr(data-label)] sm:before:content-none before:text-[0.625rem] before:uppercase before:font-medium"
                            data-label="Sub Total">{{ a.total }}</td>
                        <td class="flex flex-col px-4 py-2 sm:table-cell sm:py-4 before:content-[attr(data-label)] sm:before:content-none before:text-[0.625rem] before:uppercase before:font-medium sm:pr-6"
                            data-label="Descuento">{{ a.descuento }}</td>
                        <td class="flex flex-col px-4 py-2 sm:table-cell sm:py-4 before:content-[attr(data-label)] sm:before:content-none before:text-[0.625rem] before:uppercase before:font-medium"
                            data-label="Total">{{ a.total - a.descuento }}</td>
                        <td class="flex flex-col px-4 py-2 sm:table-cell sm:py-4 before:content-[attr(data-label)] sm:before:content-none before:text-[0.625rem] before:uppercase before:font-medium"
                            data-label="">
                            <a :href="route('detallescotizacion.show', { id: a.id })" srel="noopener noreferrer"
                            class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                                Generar Contrata
                            </a>
                        </td>
                        <td class="flex flex-col px-4 py-2 sm:table-cell sm:py-4 before:content-[attr(data-label)] sm:before:content-none before:text-[0.625rem] before:uppercase before:font-medium"
                            data-label="Total">
                            <a :href="route('cotizaciones.pdf', { id: a.id })" target="_blank" rel="noopener noreferrer"
                                :class="{ 'active-class': route().current('cotizaciones.pdf', { id: a.id }) }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </a>
                        </td>
                    </tr>

                </tbody>
            </table>
            <div class="flex justify-between mt-4">
                <button @click="prevPage" :disabled="currentPage === 1"
                    class="px-4 py-2 text-white bg-blue-500 rounded">Anterior</button>
                <span>Página {{ currentPage }} de {{ totalPages }}</span>
                <button @click="nextPage" :disabled="currentPage === totalPages"
                    class="px-4 py-2 text-white bg-blue-500 rounded">Siguiente</button>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
