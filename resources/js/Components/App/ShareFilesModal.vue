<template>
    <Modal :show="modelValue" @show="onShow" max-width="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Share Files
            </h2>
            <div class="mt-6">
                <InputLabel for="shareEmail" value="Enter Email Adresses" class="sr-only"/>
                <TextInput type="text"
                id="shareEmail"
                ref="emailInput"
                v-model="form.emails"
                class="mt-1 block w-full"
                placeholder="Enter Emails Adresses"
                @keyup.enter="shareFiles"
                />
            </div>
            <div class="mt-6 flex justify-end">
            <SecondaryButton @click="closeModal">
                Cancel
            </SecondaryButton>
            <PrimaryButton :disable="form.processing" 
            :class="{'opacity-25': form.processing }"
            class="ml-3" @click="shareFiles">
                Share
            </PrimaryButton>
        </div>

        </div>
    </Modal>
</template>


<script setup>
// Imports
import Modal from '@/Components/Modal.vue';
import TextInput from '../TextInput.vue';
import InputLabel from '../InputLabel.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import SecondaryButton from '../SecondaryButton.vue';
import PrimaryButton from '../PrimaryButton.vue';
import {ref, nextTick} from 'vue'

// Props
const {modelValue} = defineProps({
    modelValue: Boolean
})

// Emits
const emit = defineEmits(['update:modelvalue']);

// Uses
const form = useForm({
    emails: [],
    file_ids: []
});

const page = usePage();

//Refs
const emailInput = ref(null);

// Methods
function shareFiles(){
    form.post(route('folder.create'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset()
        },
        onError: () => emailInput.value.focus()
    })
}

function onShow()
{
    nextTick(() => emailInput.value.focus());
}

function closeModal(){
    emit('update:modelValue');
    form.clearErrors();
    form.reset();
}
</script>