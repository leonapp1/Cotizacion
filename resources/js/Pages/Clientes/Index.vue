<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DarkButton from '@/Components/DarkButton.vue';
import WarningButton from '@/Components/WarningButton.vue';
import InputIcons from '@/Components/InputIcons.vue';
import TextArea from '@/Components/TextArea.vue';
import { ref, computed } from 'vue';
import * as XLSX from 'xlsx';

const props = defineProps({ clientes: Object });
console.log(props)
const form = useForm({
    dni: '',
    nombre: '',
    apellido: '',
    email: '',
    telefono: '',
    direccion: ''
})
const v = ref({
    dni: '',
    nombre: '',
    apellido: '',
    email: '',
    telefono: '',
    direccion: ''
})

const showmodalview = ref(false);
const showmodalform = ref(false);
const showmodaldelet = ref(false);

const title = ref('');
const operation = ref(1);
const msj = ref('');
const clasMsj = ref('hidden');

const OpenModalView = (a) => {
    showmodalview.value = true
    v.id = a.id;
};
const openModalform = (op, a) => {
    showmodalform.value = true
    operation.value = op;
    if (op === 1) {
        title.value = "Registrar Cliente"
        form.reset();

    } else {
        title.value = "Actualizar cliente"
        form.id = a.id
        form.dni = a.dni;
        form.nombre = a.nombre;
        form.apellido = a.apellido;
        form.email = a.email
        form.telefono = a.telefono
        form.direccion = a.direccion
    }
};

const CloseModalView = () => {
    showmodalview.value = false
};
const CloseModalForm = () => {
    showmodalform.value = false
    form.reset();

};
const CloseModalDelet = () => {
    showmodaldelet.value = false;
};

const save = () => {
    if (operation.value == 1) {
        form.post(route('clientes.store'), {
            onSuccess: () => { ok("clientes Creado") }
        });

    } else {
        form.put(route('clientes.update', form.id), {
            onSuccess: () => { ok("clientes Actualizado") }
        });
    }
};

const delet = (id) => {
    form.delete(route('clientes.update', id), {
        onSuccess: () => { ok("clientes Eliminado") }
    });
};

const ok = (m) => {
    if (operation.value == 2) {
        CloseModalForm();
    }
    CloseModalForm();
    CloseModalDelet();
    form.reset();
    msj.value = m;
    clasMsj.value = '';
    setTimeout(() => {
        clasMsj.value = 'hidden'
    }, 8000)
}

const currentPage = ref(1);
const itemsPerPage = 10;
const searchQuery = ref('');

const filteredClientes = computed(() => {
    return props.clientes.filter(cliente => {
        return (
            cliente.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            cliente.apellido.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            cliente.id.toString().includes(searchQuery.value) ||
            cliente.dni.toString().includes(searchQuery.value)
        );
    }).sort((a, b) => (b.total - b.descuento) - (a.total - a.descuento));
});

const paginatedClientes = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredClientes.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredClientes.value.length / itemsPerPage);
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
    const ws = XLSX.utils.json_to_sheet(filteredClientes.value);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "clientes");
    XLSX.writeFile(wb, "clientess.xlsx");
};
const searchDni = async () => {
    // Determinar la longitud del DNI según el prefijo
    const dniLength = (form.dni.startsWith('10') || form.dni.startsWith('20')) ? 11 : 8;

    // Verificar si el DNI tiene la longitud requerida
    if (form.dni.length === dniLength) {
        try {
            const response = await axios.post(route('clientes.search'), { dni: form.dni });
            const data = response.data;

            if (data) {
                if (data.message === "not found" || data.message === "dni no valido") {
                    console.log(data);
                    form.nombre = '';
                    form.apellido = '';
                } else {
                    console.log(data);
                    if (data.nombres) {
                        form.nombre = data.nombres || '';
                        form.apellido = data.apellidoPaterno + ' ' + data.apellidoMaterno || '';
                    } else {
                        form.nombre = data.razonSocial || '';
                        form.apellido='-';
                        form.direccion=data.direccion;
                    }
                }
            }
        } catch (error) {
            console.error("Error al buscar el DNI:", error);
        }
    } else {
        // Esperar a que se complete el DNI y mostrar un mensaje
        console.warn(`Esperando hasta que el DNI tenga ${dniLength} caracteres.`);
    }
};


</script>

<template>
    <Head title="Clientes" />
    <AuthenticatedLayout>
        <template #header>
            Clientes
        </template>
        <div class="inline-flex w-full mb-4 overflow-hidden bg-white rounded-lg shadow-md" :class="clasMsj">
            <div class="flex items-center justify-center w-12 bg-green-500">
                <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM21.6667 28.3333H18.3334V25H21.6667V28.3333ZM21.6667 21.6666H18.3334V11.6666H21.6667V21.6666Z">
                    </path>
                </svg>
            </div>

            <div class="px-4 py-2 -mx-3">
                <div class="mx-3">
                    <span class="font-semibold text-blue-500">{{ msj }}</span>
                    <!-- <p class="text-sm text-gray-600">{{ msj }}</p> -->
                </div>
            </div>
        </div>

        <div class="min-w-full p-4 bg-white rounded-lg shadow-xs ">
            <DarkButton @click="openModalform(1, '')">Agregar</DarkButton>

            <input v-model="searchQuery" type="text" placeholder="Buscar..." class="mb-4 px-4 py-2 border rounded" />
            <button @click="downloadExcel" class="mb-4 px-4 py-2 text-white bg-green-500 rounded">Descargar Excel</button>
            <table class="min-w-full p-4 bg-white rounded-lg shadow-xs">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="p-5 text-sm font-semibold leading-6 text-left text-gray-900 capitalize rounded-t-xl">#
                        </th>
                        <th class="p-5 text-sm font-semibold leading-6 text-left text-gray-900 capitalize"> Dni</th>
                        <th class="p-5 text-sm font-semibold leading-6 text-left text-gray-900 capitalize">Nombre</th>
                        <th class="p-5 text-sm font-semibold leading-6 text-left text-gray-900 capitalize">Apellido</th>
                        <th class="p-5 text-sm font-semibold leading-6 text-left text-gray-900 capitalize">Email</th>
                        <th class="p-5 text-sm font-semibold leading-6 text-left text-gray-900 capitalize">Telefono</th>
                        <th class="p-5 text-sm font-semibold leading-6 text-left text-gray-900 capitalize">Direccion</th>
                        <th class="p-5 text-sm font-semibold leading-6 text-left text-gray-900 capitalize">ver</th>
                        <th class="p-5 text-sm font-semibold leading-6 text-left text-gray-900 capitalize">Edit</th>
                        <th class="p-5 text-sm font-semibold leading-6 text-left text-gray-900 capitalize rounded-t-xl">
                            Eliminar</th>

                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300 ">
                    <tr v-for="a, i in paginatedClientes" :key="a.id">
                        <td class="p-5 text-sm font-medium leading-6 text-gray-900 whitespace-nowrap">{{ i + 1 }}</td>
                        <td class="p-5 text-sm font-medium leading-6 text-gray-900 whitespace-nowrap">{{ a.dni }}</td>
                        <td class="p-5 text-sm font-medium leading-6 text-gray-900 whitespace-nowrap">{{ a.nombre }}</td>
                        <td class="p-5 text-sm font-medium leading-6 text-gray-900 whitespace-nowrap">{{ a.apellido }}</td>
                        <td class="p-5 text-sm font-medium leading-6 text-gray-900 whitespace-nowrap">{{ a.email }}</td>
                        <td class="p-5 text-sm font-medium leading-6 text-gray-900 whitespace-nowrap">{{ a.telefono }}</td>
                        <td class="p-5 text-sm font-medium leading-6 text-gray-900 whitespace-nowrap">{{ a.direccion }}</td>
                        <td class="p-5 text-sm font-medium leading-6 text-gray-900 whitespace-nowrap">
                            <SecondaryButton @click="OpenModalView(a)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </SecondaryButton>
                        </td>
                        <td class="p-5 text-sm font-medium leading-6 text-gray-900 whitespace-nowrap">
                            <WarningButton @click="openModalform(2, a)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </WarningButton>
                        </td>
                        <td class="p-5 text-sm font-medium leading-6 text-gray-900 whitespace-nowrap">
                            <DangerButton @click="delet(a.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>

                            </DangerButton>
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

        <Modal :show="showmodalview" @close="CloseModalView">
            <div class="p-6">
                <p>Nombre : {{ v.nombre }}</p>
            </div>
            <div class="mt-6 ">
                <SecondaryButton @click="CloseModalView">
                    cancel
                </SecondaryButton>
            </div>

        </Modal>

        <Modal :show="showmodalform" @close="CloseModalForm">

            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ title }}</h2>
                <div class="container px-4 py-8 mx-auto">
                    <h1 class="text-2xl font-bold text-center">Formulario de contacto</h1>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">

                            <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 "> DNI</label>
                            <div class="relative mb-6">

                                <input placeholder="DNI" v-model="form.dni" id="input-group-1"
                                    class="bg-gray-50 border border-gray-300 text-gray-900   text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:border-gray-600 dark:placeholder-gray-400  dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    @keyup="searchDni" />
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="size-6">
                                        <path
                                            d="M12.378 1.602a.75.75 0 0 0-.756 0L3 6.632l9 5.25 9-5.25-8.622-5.03ZM21.75 7.93l-9 5.25v9l8.628-5.032a.75.75 0 0 0 .372-.648V7.93ZM11.25 22.18v-9l-9-5.25v8.57a.75.75 0 0 0 .372.648l8.628 5.033Z" />
                                    </svg>
                                </div>
                            </div>
                            <InputError class="m-1" :message="form.errors.dni"></InputError>
                        </div>

                        <div>
                            <InputIcons text="Nombre" required="required" v-model="form.nombre">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6">
                                    <path
                                        d="M12.378 1.602a.75.75 0 0 0-.756 0L3 6.632l9 5.25 9-5.25-8.622-5.03ZM21.75 7.93l-9 5.25v9l8.628-5.032a.75.75 0 0 0 .372-.648V7.93ZM11.25 22.18v-9l-9-5.25v8.57a.75.75 0 0 0 .372.648l8.628 5.033Z" />
                                </svg>
                            </InputIcons>
                            <InputError class="m-1" :message="form.errors.nombre"></InputError>
                        </div>
                        <div>
                            <InputIcons text="Apellido" required="required" v-model="form.apellido">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6">
                                    <path
                                        d="M12.378 1.602a.75.75 0 0 0-.756 0L3 6.632l9 5.25 9-5.25-8.622-5.03ZM21.75 7.93l-9 5.25v9l8.628-5.032a.75.75 0 0 0 .372-.648V7.93ZM11.25 22.18v-9l-9-5.25v8.57a.75.75 0 0 0 .372.648l8.628 5.033Z" />
                                </svg>
                            </InputIcons>
                            <InputError class="m-1" :message="form.errors.apellido"></InputError>
                        </div>
                        <div>
                            <InputIcons text="Correo" required="required" v-model="form.email">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6">
                                    <path
                                        d="M12.378 1.602a.75.75 0 0 0-.756 0L3 6.632l9 5.25 9-5.25-8.622-5.03ZM21.75 7.93l-9 5.25v9l8.628-5.032a.75.75 0 0 0 .372-.648V7.93ZM11.25 22.18v-9l-9-5.25v8.57a.75.75 0 0 0 .372.648l8.628 5.033Z" />
                                </svg>
                            </InputIcons>
                            <InputError class="m-1" :message="form.errors.email"></InputError>
                        </div>
                        <div>
                            <InputIcons text="telefono" required="required" v-model="form.telefono">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6">
                                    <path
                                        d="M12.378 1.602a.75.75 0 0 0-.756 0L3 6.632l9 5.25 9-5.25-8.622-5.03ZM21.75 7.93l-9 5.25v9l8.628-5.032a.75.75 0 0 0 .372-.648V7.93ZM11.25 22.18v-9l-9-5.25v8.57a.75.75 0 0 0 .372.648l8.628 5.033Z" />
                                </svg>
                            </InputIcons>
                            <InputError class="m-1" :message="form.errors.telefono"></InputError>
                        </div>

                        <div class="col-span-2">
                            <TextArea text="Direccion" required="required" v-model="form.direccion">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>

                                        </TextArea>
                            <InputError class="m-1" :message="form.errors.direccion"></InputError>
                        </div>
                    </div>
                </div>

                <SecondaryButton @click="save" class="m-1">Guardar</SecondaryButton>
                <SecondaryButton class="m-1" @click="CloseModalForm">
                    cancel
                </SecondaryButton>
            </div>

        </Modal>

        <Modal :show="showmodaldelet" @close="CloseModalDelet">
            <div class="mt-6 ">
                <SecondaryButton @click="CloseModalDelet">
                    cancel
                </SecondaryButton>
            </div>

        </Modal>
    </AuthenticatedLayout>
</template>

