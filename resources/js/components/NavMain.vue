<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps<{
    items: NavItem[];
}>();

const lastClickedItem = ref<NavItem | null>(null);
const page = usePage<SharedData>();
const emit = defineEmits(['linkClicked']);
const handleLinkClick = (item: NavItem, event: MouseEvent) => {
    event.preventDefault();
    lastClickedItem.value = item;
    emit('linkClicked', item);
};

onMounted(() => {
    handleLinkClick(props.items[0], { preventDefault: () => {} } as MouseEvent);
});
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton 
                    as-child :is-active="item.href === lastClickedItem?.href"
                    :tooltip="item.title"
                >
                    <a :href="item.href" @click="handleLinkClick(item, $event)">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </a>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
