<template>
    <AuthenticatedLayout>
        <nav class="flex items-center justify-between p-1 mb-3">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li v-for="ans of ancestors.data" :key="ans.id" class="inline-flex items-center">
                    <Link v-if="!ans.parent_id" :href="route('MyFiles')"
                          class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <HomeIcon class="w-4 h-4"/>
                        My Files
                    </Link>
                    <div v-else class="flex items-center">
                        <svg aria-hidden="true" class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <Link :href="route('MyFiles', {folder: ans.path})"
                              class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                            {{ ans.name }}
                        </Link>
                    </div>
                </li>
            </ol>
            <div>
                <DownloadFilesButton class="mr-2 bg-blue-600" :all="allSelected" :ids="selectedIds"></DownloadFilesButton>
                <DeleteFilesButton :delete-all="allSelected" :delete-ids="selectedIds" @delete="onDelete"></DeleteFilesButton>
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
                            Owner
                        </th>
                        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                            Last Modified
                        </th>
                        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                            Size
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr @dblclick="openFolder(file)"
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
                            {{ file.owner }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 text-sm font-medium">
                            {{ file.updated_at }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 text-sm font-medium">
                            {{ file.size }}
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
import { router, Link, Head } from '@inertiajs/vue3'
import {HomeIcon} from '@heroicons/vue/20/solid'
import FileIcon from '@/Components/App/FileIcon.vue';
import { compile, computed, onMounted, onUpdated, ref } from 'vue';
import { all } from 'axios';
import { httpGet } from '@/Helper/http-helper';
import Checkbox from '@/Components/Checkbox.vue';
import DeleteFilesButton from '@/Components/App/DeleteFilesButton.vue';
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
function openFolder(file){
    if(!file.is_folder)
    {
        return false
    }

    router.visit(route('MyFiles', {folder: file.path}));
}

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

function onDelete()
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


