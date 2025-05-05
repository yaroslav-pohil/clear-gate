<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Check, Folder, LayoutGrid, Trash } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const emit = defineEmits(['linkClicked']);

const mainNavItems: NavItem[] = [
    {
        title: 'Open Tasks',
        href: route('tasks.pending'),
        icon: LayoutGrid,
    },
    {
        title: 'Completed Tasks',
        href: route('tasks.completed'),
        icon: Check,
    },
    {
        title: 'Trashed Tasks',
        href: route('tasks.trashed'),
        icon: Trash,
    },
    
    
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/yaroslav-pohil/clear-gate',
        icon: Folder,
    },
    {
        title: 'API Documentation',
        href: '/api.postman_collection.json',
        icon: BookOpen,
    },
];

const handleLinkClicked = (item: NavItem) => {
    emit('linkClicked', item);
};
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" @link-clicked="handleLinkClicked" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
