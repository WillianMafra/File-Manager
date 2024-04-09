<template>
    <Modal :show="modelValue" @show="onShow" max-width="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Create New Folder 
            </h2>
            <div class="mt-6">
                <InputLabel for="folderName" value="Folder Name" class="sr-only"/>
                <TextInput type="text"
                id="folderName"
                ref="folderNameInput"
                v-model="form.name"
                class="mt-1 block w-full"
                :class="form.errors.name ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                placeholder="Folder Name"
                @keyup.enter="createFolder"
                />
                <InputError :message="form.errors.name"></InputError>
            </div>
            <div class="mt-6 flex justify-end">
            <SecondaryButton @click="closeModal">
                Cancel
            </SecondaryButton>
            <PrimaryButton :disable="form.processing" 
            :class="{'opacity-25': form.processing }"
            class="ml-3" @click="createFolder">
                Submit
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
import { useForm } from '@inertiajs/vue3';
import InputError from '../InputError.vue';
import SecondaryButton from '../SecondaryButton.vue';
import PrimaryButton from '../PrimaryButton.vue';
import {ref, nextTick} from 'vue'

// Props
const {modelValue} = defineProps({
    modelValue: Boolean
})

const emit = defineEmits(['update:modelvalue']);

// uses
const form = useForm({
    name: ''
})

//Refs
const folderNameInput = ref(null);

// Methods
function createFolder(){
    form.post(route('folder.create'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset()
        },
        onError: () => folderNameInput.value.focus()
    })
}

function onShow()
{
    nextTick(() => folderNameInput.value.focus());
}

function closeModal(){
    emit('update:modelValue');
    form.clearErrors();
    form.reset();
}
</script>