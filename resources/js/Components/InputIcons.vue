<template>
    <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 "> {{text}}</label>
    <div class="relative mb-6">

        <input type="text" id="input-group-1" class="bg-gray-50 border border-gray-300 text-gray-900
        text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5
          dark:border-gray-600 dark:placeholder-gray-400
          dark:focus:ring-blue-500 dark:focus:border-blue-500"
        :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input"
         :placeholder="text" :type="type" :required="required"  :accept="accept"
             />
        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
            <slot>
            </slot>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';

defineProps(['modelValue', 'text', 'type', 'required', 'accept']);

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>
