<template>
    <button @click="download"
            class="inline-flex items-center mx-2 px-4 py-2 text-sm font-medium text-white bg-black border border-gray-200 rounded-lg hover:text-blue-700
             focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-grey-900 dark:border-black-900 dark:text-white 
             dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
             <ArrowDownTrayIcon class="w-4 h-4 mr-2"></ArrowDownTrayIcon>
        Download
    </button>

</template>

<script setup>
// Imports
import { usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline';
import { httpGet } from '@/Helper/http-helper';
// Uses
const page = usePage();

// Refs

// Props & Emit
const props = defineProps({
    all: {
        type: Boolean,
        default: false,
        required: false
    },
    ids: {
        type: Array,
        required: false
    }
})

// Computed

// Methods

function download()
{
    if(!props.all && props.ids.length === 0){
        return;
    }

    const p = new URLSearchParams();

    p.append('parent_id', page.props.folder.data.id);
    if(props.all){
        p.append('all', props.all ? 1 : 0);
    } else {
        for(let id of props.ids){
            p.append('ids[]', id);
        }
    }

    httpGet(route('file.download') + '?' + p.toString())
    .then(response => {
        if(!response.url) return

        const a = document.createElement('a');
        a.download = response.filename;
        a.href = response.url;

        a.click();
    });
}

// Hooks
</script>