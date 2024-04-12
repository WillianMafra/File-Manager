<template>
    <Menu as="div" class="relative block text-left">
        <MenuButton
          class="flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2
           text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
        >
          Create New
        </MenuButton>
      <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <MenuItems
          class="absolute left-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
        >
          <div class="px-1 py-1">
            <MenuItem v-slot="{ active }">
                <a @click.prevent="showCreateFolderModal" href="#" class="text-gray-700 block px-4 py-2 text-sm">New Folder</a>
          </MenuItem>
          </div>
            <FileUploadMenuItem></FileUploadMenuItem>
            <FolderUploadMenuItem></FolderUploadMenuItem>
          <div class="px-1 py-1">
          </div>
        </MenuItems>
      </transition>
    </Menu>
    <CreateFolderModal v-model="showCreateModal"></CreateFolderModal>
</template>

<script setup>
// Imports
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import CreateFolderModal from './CreateFolderModal.vue';
import FileUploadMenuItem from '@/Components/App/FileUploadMenuItem.vue';
import FolderUploadMenuItem from '@/Components/App/FolderUploadMenuItem.vue';
import { ref } from 'vue';

// Refs
const showCreateModal = ref(false);

// Methods
function showCreateFolderModal(){
  showCreateModal.value = true;
}



</script>
