<template>
    <PrimaryButton @click="download">
        <ArrowDownTrayIcon class="w-6 h-6 mr-2"></ArrowDownTrayIcon>
        Download
    </PrimaryButton>

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
        defaul: false,
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