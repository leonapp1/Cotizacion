<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
      <template #header>
        Dashboard
      </template>
      <div class="p-4 bg-white rounded-lg shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Clientes</h2>
            <p>Total: {{ clientes.length }}</p>
            <h2 class="text-lg font-semibold mb-4">Productos</h2>
            <p>Total: {{ productos.length }}</p>
          </div>

          <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Cotizaciones</h2>
            <p>Total: {{ cotizaciones.length }}</p>
            <div class="relative w-full h-96">
              <Bar :data="cotizacionChartData" :options="chartOptions" />
            </div>
          </div>
          <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Contratos</h2>
            <p>Total: {{ contratos.length }}</p>
            <div class="relative w-full h-96">
              <Doughnut :data="contratoChartData" :options="chartOptions" />
            </div>
          </div>
          <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Productos Más Vendidos</h2>
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                  <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad Vendida</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="producto in productosMasVendidos" :key="producto.productoid">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ producto.producto.nombre }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ producto.total_cantidad }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>

  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import { Head } from '@inertiajs/vue3';
  import { ref, watch } from 'vue';
  import { Bar, Doughnut } from 'vue-chartjs';
  import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, BarElement, CategoryScale, LinearScale } from 'chart.js';

  ChartJS.register(Title, Tooltip, Legend, ArcElement, BarElement, CategoryScale, LinearScale);

  const props = defineProps({
    clientes: { type: Array, required: true },
    productos: { type: Array, required: true },
    cotizaciones: { type: Array, required: true },
    contratos: { type: Array, required: true },
    productosMasVendidos: { type: Array, required: true }
  });
console.log(props.contratos)
  const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false
  });

  const cotizacionChartData = ref({
    labels: props.cotizaciones.map(c => c.requerimiento),
    datasets: [{
      label: 'Cotizaciones',
      data: props.cotizaciones.map(c => c.total),
      backgroundColor: props.cotizaciones.map(() => `rgb(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)})`)
    }]
  });

  const contratoChartData = ref({
    labels: props.contratos.map(c => c.cliente.nombre),
    datasets: [{
      label: 'Contratos',
      data: props.contratos.map(c => c.total),
      backgroundColor: props.contratos.map(() =>
      `rgb(${Math.floor(Math.random() * 256)},
       ${Math.floor(Math.random() * 256)},
        ${Math.floor(Math.random() * 256)})`)
    }]
  });
  </script>

  <style scoped>
  /* Estilos adicionales aquí */
  </style>
