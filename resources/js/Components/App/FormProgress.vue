<template>
  <div v-if="form.progress" class="absolute border-gray-900 vorder right-8 bottom-4 bg-white rounded
  flex flex-col w-[240px] p-2">
    <div>Uploading {{ form.files.length }} files</div>
    <div class="h-3 bg-gray-100 rounded-md">
        <div class="h-full bg-gray-900 rounded-md transition-all " :style="{width: form.progress.percentage}+'%'">
        </div>
    </div>
  </div>
</template>

<script setup>
// Imports

// Uses

// Refs

// Props & Emit
const props = defineProps({
    form: Object
});

// Computed

// Methods

// Hooks
</script>

<style scoped>
  
</style>