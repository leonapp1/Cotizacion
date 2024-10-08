<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputIcons from '@/Components/InputIcons.vue';

const props = defineProps({
    contratos: Object,
    productos: Object,
    clientes: Object,
    observaciones: Object,
    valor: [String, Number]
});

const title = ref('');
const operation = ref(1);
const msj = ref('');
const clasMsj = ref('hidden');

const showmodalview = ref(false);

const OpenModalView = () => {
    showmodalview.value = true
    console.log(obscheck)
};

const CloseModalView = () => {
    showmodalview.value = false
};

const form = useForm({
    clienteid: '',
    total: '', // Total antes del descuento
    total_descuento: 0, // Nuevo campo para el total con descuento
    descuento: 0,
    requerimiento: '',
    ubicacion: '',
    departamento: '',
    provincia: '',
    distrito: '',
    detallecontratos: [],
    obscheck: [],
    pagos: [],
});

const list = ref([{
    cantidad: '',
    precio_unitario: '',
    subtotal: 0,
    productoid: ''
}]);

const paymentsList = ref([{ // Lista de pagos ficticios para mostrar inicialmente
    no_pago: 1,
    descripcion: '',
    cantidad: 0
}]);

const obscheck = ref([]);

const eliminarEntrada = (index) => {
    list.value.splice(index, 1);
};

const agregarEntrada = () => {
    list.value.push({
        cantidad: '',
        precio_unitario: '',
        subtotal: 0,
        productoid: ''
    });
};

// Agregar Pago
const agregarPago = () => {
    const no_pago = paymentsList.value.length + 1;
    paymentsList.value.push({
        no_pago,
        descripcion: '', // Esto será seleccionado por el usuario
        cantidad: 0
    });
};

// Eliminar Pago
const eliminarPago = (index) => {
    paymentsList.value.splice(index, 1);
};

// Validar si los pagos son correctos
const validarPagos = () => {
    const pagosInvalidos = paymentsList.value.filter(pago => !pago.descripcion || isNaN(pago.cantidad) || pago.cantidad <= 0);
    if (pagosInvalidos.length > 0) {
        return false; // Al menos un pago es inválido
    }
    const totalPagos = paymentsList.value.reduce((sum, pago) => sum + (parseFloat(pago.cantidad) || 0), 0);
    return totalPagos <= form.total; // Validar que los pagos no excedan el total
};

watch(
    () => list.value.map(entry => [entry.cantidad, entry.precio_unitario]),
    (newVal) => {
        newVal.forEach((values, index) => {
            const [cantidad, precio_unitario] = values;
            list.value[index].subtotal = (parseFloat(cantidad) || 0) * (parseFloat(precio_unitario) || 0);
        });
    },
    { deep: true }
);

// Cálculo del total y saldo sobrante
const totalSum = computed(() => {
    const totalSinDescuento = list.value.reduce((sum, entry) => sum + (parseFloat(entry.subtotal) || 0), 0);
    const descuentoValor = parseFloat(form.descuento) || 0;
    form.total = totalSinDescuento;
    form.total_descuento = totalSinDescuento - descuentoValor; // Calcula y asigna el total con descuento
    return formatCurrency(form.total_descuento);
});

const montoSobrante = computed(() => {
    const totalPagos = paymentsList.value.reduce((sum, pago) => sum + (parseFloat(pago.cantidad) || 0), 0);
    const sobrante = form.total_descuento - totalPagos; // Usar total con descuento
    return sobrante >= 0 ? formatCurrency(sobrante) : "Error: pagos exceden el total";
});

const guardar_pdf = () => {
    form.detallecontratos = list.value;
    form.pagos = paymentsList.value; // Guardamos los pagos en el formulario

    const options = {
        onSuccess: () => {
            ok("Cotización guardada");
            const url = operation.value == 1 ? `/contratos/${props.valor}/pdf` : `/contratos/${form.id}/pdf`;
            window.open(url, '_blank');
        }
    };

    if (operation.value == 1) {
        form.post(route('contratos.store'), options); // Enviar también el campo total_descuento
    } else {
        form.put(route('contratos.update', form.id), options);
    }
};


const ok = (m) => {
    form.reset();
    list.value = ([{
        cantidad: '',
        precio_unitario: '',
        subtotal: 0,
        productoid: ''
    }]);
    paymentsList.value = ([{
        no_pago: 1,
        descripcion: '',
        cantidad: 0
    }]);
    msj.value = m;
    clasMsj.value = '';
    setTimeout(() => {
        clasMsj.value = 'hidden';
    }, 8000);
};

const guardar_pdf_validado = () => {
    const entradasInvalidas = list.value.filter(entrada => !validarEntrada(entrada));

    if (entradasInvalidas.length === 0 && validarPagos() && form.clienteid && form.requerimiento && form.ubicacion && form.departamento && form.provincia && form.distrito) {
        guardar_pdf();
    } else {
        msj.value = 'Por favor, asegúrese de que todas las entradas y pagos sean válidos.';
        clasMsj.value = '';
        setTimeout(() => {
            clasMsj.value = 'hidden';
        }, 5000);
    }
};

const validarEntrada = (entrada) => {
    return entrada.cantidad && !isNaN(entrada.cantidad) && entrada.precio_unitario && !isNaN(entrada.precio_unitario) && entrada.productoid;
};

const actualizarPrecio = (productId, index) => {
    const producto = props.productos.find(p => p.id === productId);
    if (producto) {
        list.value[index].precio_unitario = producto.precio;
    } else {
        list.value[index].precio_unitario = '';
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN',
    }).format(value);
};
const productosSeleccionados = computed(() => {
    return list.value
        .filter(entrada => entrada.productoid)
        .map(entrada => {
            const producto = props.productos.find(p => p.id === entrada.productoid);
            return {
                id: producto.id,
                nombre: producto.nombre
            };
        });
});
</script>

<template>

    <Head title="Contratos" />
    <AuthenticatedLayout>
        <template #header>
            Contratos
        </template>
        <div class="min-w-full p-4 bg-white rounded-lg shadow-xs">
            <div v-if="msj" class="inline-flex w-full mb-4 overflow-hidden bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-center w-12 bg-Red-500">
                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z">
                        </path>
                    </svg>
                </div>
                <div class="px-4 py-2 -mx-3">
                    <div class="mx-3">
                        <span class="font-semibold text-blue-500">{{ msj }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="min-w-full p-4 bg-white rounded-lg shadow-xs">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 "> Cliente</label>
                    <div class="relative mb-6">
                        <v-select class="v-selct" label="nombre" :options="clientes" placeholder="Selecciona Producto"
                            :reduce="cliente => cliente.id" v-model="form.clienteid">
                            <template #option="props">
                                <div>
                                    {{ props.dni }} - {{ props.nombre }} {{ props.apellido }}
                                </div>
                            </template>
                            <template #selected-option="props">
                                <div>
                                    {{ props.dni }} - {{ props.nombre }} {{ props.apellido }}
                                </div>
                            </template>
                        </v-select>
                    </div>
                </div>
                <div>
                    <InputIcons text="Requerimiento" required="required" v-model="form.requerimiento">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </InputIcons>
                    <InputError class="m-1" :message="form.errors.requerimiento"></InputError>
                </div>

                <div>
                    <InputIcons text="Ubicación" required="required" v-model="form.ubicacion">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </InputIcons>
                    <InputError class="m-1" :message="form.errors.ubicacion"></InputError>
                </div>

                <div>
                    <InputIcons text="Departamento" required="required" v-model="form.departamento">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </InputIcons>
                    <InputError class="m-1" :message="form.errors.departamento"></InputError>
                </div>

                <div>
                    <InputIcons text="Provincia" required="required" v-model="form.provincia">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </InputIcons>
                    <InputError class="m-1" :message="form.errors.provincia"></InputError>
                </div>

                <div>
                    <InputIcons text="Distrito" required="required" v-model="form.distrito">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </InputIcons>
                    <InputError class="m-1" :message="form.errors.distrito"></InputError>
                </div>

            </div>
        </div>
        <div class="min-w-full p-6 overflow-x-auto bg-white rounded-lg shadow-md">
            <button @click="agregarEntrada"
                class="px-4 py-2 mb-4 font-semibold text-white transition duration-200 bg-blue-500 rounded hover:bg-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd"
                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z"
                        clip-rule="evenodd" />
                </svg>

            </button>

            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="text-left bg-gray-100">
                        <th class="px-4 py-3 font-semibold border-b">Producto</th>
                        <th class="px-4 py-3 font-semibold border-b">Cantidad</th>
                        <th class="px-4 py-3 font-semibold border-b">Precio Unitario</th>
                        <th class="px-4 py-3 font-semibold border-b">SubTotal</th>
                        <th class="px-4 py-3 font-semibold border-b">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(entrada, index) in list" :key="index" class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b">
                            <v-select label="nombre" class="w-full" :options="productos"
                                :reduce="producto => producto.id" placeholder="Selecciona Producto"
                                v-model="entrada.productoid"
                                @update:modelValue="(value) => actualizarPrecio(value, index)">
                                <template #option="props">
                                    <div class="p-2"><img :src="props.img" alt="" class="w-20 h-20"></div>
                                    <div class="p-2">{{ props.nombre }}</div>
                                </template>
                                <template #selected-option="props">
                                    <div class="p-2 font-semibold">{{ props.nombre }}</div>
                                </template>
                            </v-select>
                        </td>
                        <td class="px-4 py-3 border-b">
                            <input class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-300"
                                type="number" v-model="entrada.cantidad" placeholder="Cantidad">
                        </td>
                        <td class="px-4 py-3 border-b">
                            <input class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-300"
                                type="number" v-model="entrada.precio_unitario" placeholder="Precio Unitario">
                        </td>
                        <td class="px-4 py-3 border-b">
                            <input class="w-full p-2 bg-gray-100 border rounded" type="text" :value="entrada.subtotal"
                                placeholder="Subtotal" disabled>
                        </td>
                        <td class="px-4 py-3 border-b">
                            <button @click="eliminarEntrada(index)" class="text-red-600 hover:text-red-800">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Sección de Pagos -->
            <!-- Pagos Section -->
            <div class="min-w-full p-6 mt-6 bg-white rounded-lg shadow-md">
                <h2 class="mb-4 text-xl font-semibold text-gray-800">Pagos</h2>

                <!-- Tabla de Pagos -->
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="text-left bg-gray-100">
                            <th class="px-4 py-3 font-semibold border-b">N° Pago</th>
                            <th class="px-4 py-3 font-semibold border-b">Descripción</th>
                            <th class="px-4 py-3 font-semibold border-b">Cantidad</th>
                            <th class="px-4 py-3 font-semibold border-b">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(pago, index) in paymentsList" :key="index" class="hover:bg-gray-50">
                            <td class="px-4 py-3 border-b">{{ pago.no_pago }}</td>
                            <td class="px-4 py-3 border-b">
                                <select v-model="pago.descripcion"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option disabled value="">Selecciona un producto</option>
                                    <option v-for="producto in productosSeleccionados" :key="producto.id"
                                        :value="producto.nombre">
                                        {{ producto.nombre }}
                                    </option>
                                </select>
                            </td>
                            <td class="px-4 py-3 border-b">
                                <input
                                    class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-300"
                                    type="number" v-model="pago.cantidad" placeholder="Cantidad">
                            </td>
                            <td class="px-4 py-3 border-b">
                                <button @click="eliminarPago(index)" class="text-red-600 hover:text-red-800">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4">
                    <button @click="agregarPago"
                        class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-700">
                        Agregar Pago
                    </button>
                </div>

                <!-- Monto sobrante -->
                <div class="mt-4">
                    <p class="text-lg font-semibold">Monto sobrante: <span class="text-blue-600">{{ montoSobrante
                            }}</span></p>
                    <p v-if="montoSobrante === 'Error: pagos exceden el total'" class="text-red-600">Error: La suma de
                        los pagos
                        excede el total del contrato.</p>
                </div>
                <div class="flex flex-col items-center mt-4 md:flex-row md:justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <span class="mr-2 font-bold">Total:</span>
                        <span class="text-lg font-semibold">{{ totalSum }}</span>
                    </div>
                    <div class="flex items-center">
                        <InputIcons text="Descuento" required="required" v-model="form.descuento">
                            S/.
                        </InputIcons>
                        <InputError class="m-1" :message="form.errors.descuento"></InputError>
                    </div>

                    <SecondaryButton @click="OpenModalView()">
                        Agregar Condiciones Generales
                    </SecondaryButton>
                </div>

                <button @click="guardar_pdf_validado"
                    class="px-4 py-2 mt-4 font-semibold text-white transition duration-200 bg-green-500 rounded hover:bg-green-600">
                    Guardar y generar PDF
                </button>

            </div>
            <Modal :show="showmodalview" @close="CloseModalView">
                <div class="p-6">
                    <div v-for="obs in observaciones" :key="obs.id"
                        class="flex items-center border border-gray-200 rounded ps-4 dark:border-gray-700">
                        <input :id="'checkbox-' + obs.id" type="checkbox" :value="obs.id" name="bordered-checkbox"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            v-model="form.obscheck" />
                        <label :for="'checkbox-' + obs.id"
                            class="w-full py-4 text-sm font-medium text-gray-900 ms-2 dark:text-gray-300">
                            {{ obs.nombre }}
                        </label>
                    </div>
                </div>
                <div class="mt-6 ">
                    <SecondaryButton @click="CloseModalView">
                        cancel
                    </SecondaryButton>
                </div>
            </Modal>
        </div>
    </AuthenticatedLayout>
</template>


<style>
.vs__dropdown-toggle {
    padding: 4.5px;
    border-color: rgb(75 85 99) !important;
    color: rgb(17 24 39 / var(--tw-text-opacity));
    background-color: rgb(249 250 251 / var(--tw-bg-opacity)) !important;
    border: 1px solid rgb(0 0 0) !important;
}
</style>
