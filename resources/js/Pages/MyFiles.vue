<script setup>
    import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';

    // Props
    const {files} = defineProps({
        files: Object
    })
</script>

<template>
    <div>
    <AuthenticatedLayout>
        <table class="min-w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
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
                <tr v-if="files" v-for="file of files.data" :key="file.id" class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 text-sm font-medium">
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
    </AuthenticatedLayout>    
    </div>
</template>

<style>
    
</style>