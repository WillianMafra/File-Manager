<template>
    <AuthenticatedLayout>
        <nav class="flex items-center justify-end p-1 mb-3">
            <div>
                <DownloadFilesButton :shared-with-me="true" class="bg-blue-600" :all="allSelected" :ids="selectedIds"></DownloadFilesButton>
            </div>
        </nav>
        <div class="flex-1 overflow-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="text-sm font-medium text-gray-900 px-6 pr-0 w-[30px] max-w-[30px] text-left">
                            <Checkbox @change="onSelectAllChange" v-model="allSelected" :checked="allSelected"></Checkbox>
                        </th>
                        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                            Name
                        </th>
                        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                            Path
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                    @click="$event => toggleFileSelect(file)" 
                    v-if="allFiles"
                    v-for="file of allFiles.data" 
                    :key="file.id" 
                    class="border-b transition duration-300 ease-in-out hover:bg-blue-100 cursor-pointer"
                    :class="(selected[file.id] || allSelected) ? 'bg-green-200' : 'bg-white'">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 text-sm font-medium pr-0 w-[30px] max-w-[30px]">
                            <Checkbox @change="onSelectCheckboxChange(file)" v-model="selected[file.id]" :checked="selected[file.id] || allSelected"></Checkbox>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 text-sm font-medium">
                            <FileIcon :file="file"></FileIcon>
                            {{ file.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 text-sm font-medium">
                            {{ file.path }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!allFiles.data.length" class="py-8 text-center text-sm text-gray-400">
                There is no data in this folder
            </div>
        <div ref="loadMoreIntersect"></div>
    </div>
    </AuthenticatedLayout>   
</template>

<style>
    
</style>

<script setup>
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import FileIcon from '@/Components/App/FileIcon.vue';
import {  computed, onMounted, onUpdated, ref } from 'vue';
import { httpGet } from '@/Helper/http-helper';
import Checkbox from '@/Components/Checkbox.vue';
import DownloadFilesButton from '@/Components/App/DownloadFilesButton.vue';



// Props
const props = defineProps({
    files: Object,
    folder: Object,
    ancestors: Object
})


// Refs
const allSelected = ref(false);
const selected = ref({});
const loadMoreIntersect = ref(null);
const allFiles = ref({
    data: props.files.data,
    next: props.files.links.next
})

// Methods

function loadMore()
{
    if(allFiles.value.next === null) {
        return
    }

    httpGet(allFiles.value.next).then(result => {
        allFiles.value.data = [...allFiles.value.data, ...result.data]
        allFiles.value.next = result.links.next
    });
}

function onSelectAllChange()
{
    allFiles.value.data.forEach(f => {
        selected.value[f.id] = allSelected.value
    })
}

function toggleFileSelect(file)
{
    selected.value[file.id] = !selected.value[file.id];
    onSelectCheckboxChange(file);
}

function onSelectCheckboxChange(file)
{
    if(!selected.value[file.id]){
        allSelected.value = false
    } else {
        let checked = true;

        for (let file of allFiles.value.data){
            if(!selected.value[file.id]){
                checked = false;
                break
            }
        }

        allSelected.value = checked;
    }
}

function resetForm()
{
    allSelected.value = false;
    selected.value = [];
}

// Hooks
onUpdated( () => {
    allFiles.value = {
        data: props.files.data,
        next: props.files.links.next
    }
})

onMounted( () => {
    const observer = new IntersectionObserver((entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), {
        rootMargin: '-250px 0px 0px 0px'
    })

    observer.observe(loadMoreIntersect.value)
});

// Computed
const selectedIds = computed( () => 
    Object.entries(selected.value).filter(a => a[1]).map(a => a[0])
)
</script>


