<template>
       <button @click="onClick"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-blue-900 border border-gray-200 rounded-lg hover:text-blue-700
             focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-blue-500 dark:border-black-900 dark:text-white 
             dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
        </svg>

        RESTORE
    </button>
    <ConfirmationDialog 
    :show="showConfirmationDialog"
    :message="'Are you sure you want to restore selected files?'"
    @cancel="onCancel"
    @confirm="onConfirm"
    >
    
    </ConfirmationDialog>

</template>

<script setup>
// Imports
import { useForm, usePage } from '@inertiajs/vue3';
import ConfirmationDialog from '../ConfirmationDialog.vue';
import { ref } from 'vue';
import { showErrorDialog, showSuccessNotification } from '@/event-bus';

// Uses
const page = usePage();
const form = useForm({
    all: null,
    ids: [],
    parent_id: null
});

// Refs
const showConfirmationDialog = ref(false);

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
        showErrorDialog('Please select at least one file to restore.')
        return;
    }
    showConfirmationDialog.value = true;
}

function onCancel()
{
    showConfirmationDialog.value = false;
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

    form.post(route('file.restore'), {
        onSuccess: () => {
            showConfirmationDialog.value = false;
            emit('restore');
            showSuccessNotification('Selected files have been restored')
        }
    }
);

}


// Hooks
</script>