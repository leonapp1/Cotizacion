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

const props = defineProps({ productos: Object });

const form = useForm({
    id: '',
    nombre: '',
    descripcion: '',
    precio: '',
    img:''
})
const v = ref({
    nombre: '',
    descripcion: '',
    precio: '',
    id: '',
    img:''
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
    v.value.nombre = a.nombre
    v.value.id = a.id
    console.log(a)
};
const openModalform = (op, a) => {
    showmodalform.value = true
    operation.value = op;
    if (op === 1) {
        title.value = "Registrar Producto"
        form.reset();

    } else {
        title.value = "Actualizar Producto"
        form.nombre = a.nombre;
        form.descripcion = a.descripcion;
        form.precio = a.precio;
        form.id = a.id
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
        form.post(route('productos.store'), {
            onSuccess: () => { ok("producto Creado") }
        });

    } else {
        form.post(route('productos.update', form.id), {
            onSuccess: () => { ok("producto Actualizado") }
        });
        console.log(form.nombre)
        console.log(form.id)
        console.log(form.descripcion)
        console.log(form.precio)
    }
    
};

const delet = (id) => {
    form.delete(route('productos.update', id), {
        onSuccess: () => { ok("producto Eliminado") }
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

const filteredProductos = computed(() => {
    return props.productos.filter(producto => {
        return (
            producto.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            producto.apellido.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            producto.id.toString().includes(searchQuery.value)
        );
    }).sort((a, b) => (b.total - b.descuento) - (a.total - a.descuento));
});

const paginatedProductos = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredProductos.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredProductos.value.length / itemsPerPage);
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
    const ws = XLSX.utils.json_to_sheet(filteredProductos.value);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Productos");
    XLSX.writeFile(wb, "productos.xlsx");
};
const handleFileUpload = (event) => {
    form.img = event.target.files[0];
   
};
</script>

<template>
    <Head title="Productos" />
    <AuthenticatedLayout>
        <template #header>
            Productos
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

        <div class="p-4 bg-white rounded-lg shadow-xs">
            <DarkButton @click="openModalform(1, '')">Agregar</DarkButton>
            <input v-model="searchQuery" type="text" placeholder="Buscar..." class="mb-4 px-4 py-2 border rounded" />
            <button @click="downloadExcel" class="mb-4 px-4 py-2 text-white bg-green-500 rounded">Descargar Excel</button>
            <table class="w-full mt-4 border-collapse">
                <thead>
                    <tr>
                        <th class="p-3 font-bold text-center text-white bg-gray-800">#</th>
                        <th class="p-3 font-bold text-center text-white bg-gray-800">imagen</th>
                        <th class="p-3 font-bold text-center text-white bg-gray-800">Nombre</th>
                        <th class="p-3 font-bold text-center text-white bg-gray-800">Descripción</th>
                        <th class="p-3 font-bold text-center text-white bg-gray-800">Precio</th>
                        <th class="p-3 font-bold text-center text-white bg-gray-800">ver</th>
                        <th class="p-3 font-bold text-center text-white bg-gray-800">Edit</th>
                        <th class="p-3 font-bold text-center text-white bg-gray-800">Eliminar</th>

                    </tr>
                </thead>
                <tbody>
                    <tr v-for="a, i in paginatedProductos" :key="a.id">
                        <td class="p-2 text-left border">{{ i + 1 }}</td>
                        <td class="p-2 border text-center">
                            <div class="flex items-center justify-center">
                                <img :src="a.img" alt="" class="w-20 h-20" >
                            </div>
                        </td>
                        <td class="p-2 text-left border">{{ a.nombre }}</td>
                        <td class="p-2 text-left border">{{ a.descripcion }}</td>
                        <td class="p-2 text-right border">{{ a.precio }}</td>
                        <td class="p-2 text-center border">
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
                        <td class="p-2 text-center border">
                            <WarningButton @click="openModalform(2, a)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </WarningButton>
                        </td>
                        <td class="p-2 text-center border">
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
                <InputIcons text="Nombre" required="required" v-model="form.nombre">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path
                            d="M12.378 1.602a.75.75 0 0 0-.756 0L3 6.632l9 5.25 9-5.25-8.622-5.03ZM21.75 7.93l-9 5.25v9l8.628-5.032a.75.75 0 0 0 .372-.648V7.93ZM11.25 22.18v-9l-9-5.25v8.57a.75.75 0 0 0 .372.648l8.628 5.033Z" />
                    </svg>
                </InputIcons>
                <InputError class="m-1" :message="form.errors.nombre"></InputError>

                <TextArea text="Descripcion" required="required" v-model="form.descripcion">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>

                    </TextArea>
                <InputError class="m-1" :message="form.errors.descripcion"></InputError>


                <InputIcons text="Precio" required="required" v-model="form.precio">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                </InputIcons>
                <InputError class="" :message="form.errors.precio"></InputError>
                
                <label class="block  text-sm font-medium text-gray-90" for="large_size">imagen</label>
                <input @change="handleFileUpload" accept="image/*" class="mb-2 block w-full text-lg text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="large_size" type="file">
                <InputError class="" :message="form.errors.img"></InputError>
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

