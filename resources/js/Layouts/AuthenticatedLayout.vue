<template>
    <div class="h-screen bg-gray-50 flex w-full gap-4">
        <Navigation />

        <main @drop.prevent="handleDrop"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave"
            class="flex flex-col flex-1 px-4 overflow-hidden"
            :class="dragOver ? 'dropzone' : ''" >
            <template v-if="dragOver" class="text-gray-500 text-center py-8 text-sm">
                Drop files here to upload
            </template>
            <template v-else>
                <div class="flex items-center justify-between w-full">
                    <SearchForm />
                    <UserSettingsDropdown />
                </div>
                <div class="flex flex-1 flex-col overflow-hidden">
                    <slot></slot>
                </div>
            </template>
        </main>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import Navigation from '@/Components/App/Navigation.vue';
import SearchForm from '@/Components/App/SearchForm.vue';
import UserSettingsDropdown from '@/Components/App/UserSettingsDropdown.vue'
import { FILE_UPLOAD_STARTED, emitter } from '@/event-bus';
import { useForm, usePage } from '@inertiajs/vue3'
import { formToJSON } from 'axios';

const showingNavigationDropdown = ref(false);

// Uses
const fileUploadForm = useForm({
    files: [],
    parent_id: null,
    relative_paths: []
});

const page = usePage();

// Refs
const dragOver = ref(false);

// Methods


function handleDrop(ev){
    dragOver.value = false;
    const files = ev.dataTransfer.files

    if (!files.length){
        return
    }

    uploadFiles(files);
}
function onDragOver(){
    dragOver.value = true;
}
function onDragLeave(){
    dragOver.value = false;
}


function uploadFiles(files)
{

    fileUploadForm.parent_id = page.props.folder.data.id
    fileUploadForm.files = files;
    fileUploadForm.relative_paths = [...files].map(f => f.webkitRelativePath); 

    fileUploadForm.post(route('file.store'));
}




// Hooks

onMounted(() => {
    emitter.on(FILE_UPLOAD_STARTED, uploadFiles)
})


</script>

<style scoped>
    .dropzone{
        width: 100%;
        height: 100%;
        color: #8d8d8d;
        border: 2px dashed gray;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>