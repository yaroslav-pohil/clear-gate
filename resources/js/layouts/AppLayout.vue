<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType, NavItem } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const emit = defineEmits(['linkClicked']);

const handleLinkClicked = (item: NavItem) => {
    emit('linkClicked', item);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" @link-clicked="handleLinkClicked">
        <slot />
    </AppLayout>
</template>
