<template>
    <nav class="min-w-[200px]">
        <div >
            <Link class="h-[80px] flex px-3 items-center gap-3" href="/">
                <ApplicationLogo
                    class="block h-9 ml-1 w-auto fill-current text-gray-800"
                />
                WillStorage
            </Link>
        </div>
        <div class="px-3">
            <CreateNewDropdown />
            <div class="py-3">
                <NavLink :active="$page.props.route_name == 'MyFiles'" :href="route('MyFiles')">My Files</NavLink>
                <NavLink :active="$page.props.route_name == 'file.sharedWithMe'" :href="route('file.sharedWithMe')">Shared with me</NavLink>
                <NavLink :active="$page.props.route_name == 'file.sharedByMe'" :href="route('file.sharedByMe')">Shared by me</NavLink>
                <NavLink :active="$page.props.route_name == 'trash'" :href="route('trash')">Trash</NavLink>
            </div>
        </div>
    </nav>
</template>

<script setup>

//Imports
import {Link} from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import NavLink from '@/Components/NavLink.vue'
import CreateNewDropdown from '@/Components/App/CreateNewDropdown.vue'

</script>

