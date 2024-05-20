<template>
    <Modal :show="props.modelValue" @show="onShow" max-width="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Share Files
            </h2>
            <div class="mt-6">
                <InputLabel for="shareEmail" value="Enter Email Adress" class="sr-only"/>
                <TextInput type="text"
                id="shareEmail"
                ref="emailInput"
                :class="form.errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                v-model="form.email"
                class="mt-1 block w-full"
                placeholder="Enter Emails Adress"
                @keyup.enter="shareFiles"
                />
                <InputError :message="form.errors.email"></InputError>

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
import InputError from '../InputError.vue';
import { showSuccessNotification } from '@/event-bus';

// Props
const props = defineProps({
    modelValue: Boolean,
    allSelected: Boolean,
    selectedIds: Array
})

// Emits
const emit = defineEmits(['update:modelValue']);

// Uses
const form = useForm({
    email: null,
    ids: [],
    all: false,
    parent_id: null
});

const page = usePage();

//Refs
const emailInput = ref(null);

// Methods
function shareFiles(){
    form.parent_id = page.props.folder.data.id;
    form.all = props.allSelected;
    if(props.allSelected){
        form.all = true,
        form.ids = []
    } else {
        form.ids = props.selectedIds
    }
    form.post(route('file.share'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessNotification('Selected files will be shared to ' + form.email + ' if the emails exists in the system ');
            closeModal();
            form.reset();

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