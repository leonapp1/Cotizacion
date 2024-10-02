<template>
    <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 "> {{ text }}</label>
    <div class="relative mb-6">
        <textarea id="message" rows="4"
            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border
             border-gray-300 focus:ring-blue-500 focus:border-blue-500
              dark:border-gray-600 dark:placeholder-gray-400
               dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Write your thoughts here..." :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)" ref="input" :placeholder="text" :type="type"
            :required="required" :accept="accept">

</textarea>
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
