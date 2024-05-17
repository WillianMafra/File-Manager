<template>
       <button @click="onClick"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-gray-200 rounded-lg 
             focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-blue-500 dark:border-black-900 dark:text-white 
             dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
            </svg>
             Share
    </button>
    <ShareFilesModal v-model="showModal"></ShareFilesModal>
</template>

<script setup>
// Imports
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import {  showErrorDialog, showSuccessNotification } from '@/event-bus';
import ShareFilesModal from '@/Components/App/ShareFilesModal.vue'

// Uses
const page = usePage();
const form = useForm({
    all: null,
    ids: [],
});

// Refs
const showModal = ref(false);

// Props & Emit
const props = defineProps({
    allSelected: {
        type: Boolean,
        defaul: false,
        required: false
    },
    selectedIds: {
        type: Array,
        required: false
    }
})

const emit = defineEmits(['restore']);
// Computed

// Methods

function onClick()
{

    if(!props.allSelected && !props.selectedIds.length) {
        showErrorDialog('Please select at least one file to share.')
        return;
    }
    showModal.value = true;
}

function onCancel()
{
    showModal.value = false;
}

function onConfirm()
{
    if(props.allSelected)
    {
        form.all = true;
        form.ids = [];
    } else {
        form.ids = props.selectedIds
    }

    form.post(route('file.share'), {
        onSuccess: () => {
            showModal.value = false;
            emit('restore');
            showSuccessNotification('Selected files have been shared')
        }
    }
);

}


// Hooks
</script>