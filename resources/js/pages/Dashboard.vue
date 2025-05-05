<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type NavItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import axios, { type AxiosResponse } from 'axios';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import { AlertCircle } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My Tasks',
        href: '/tasks',
    },
];

interface Task {
    id: number;
    title: string;
    description: string;
    completed_at: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

const newTask = {
    id: 0,
    title: '',
    description: '',
    completed_at: null,
    created_at: '',
    updated_at: '',
    deleted_at: null,
} as Task;

const currentTask = ref<Task>({ ...newTask });

const tasks = ref<Task[]>([]);
const clickedNavItem = ref<NavItem | null>(null);
const sortedTasks = computed(() => {
    return tasks.value.sort((a, b) => a.order - b.order);
});

const getTasks = () => {
    axios.get(clickedNavItem.value?.href).then((response: AxiosResponse<{ data: Task[] }>) => {
        tasks.value = response.data.data;
    });
};

// Handle link clicked in NavMain
const handleNavLinkClicked = (item: NavItem) => {
    console.log('Link clicked in NavMain:', item);
    clickedNavItem.value = item;
    getTasks();
};

const processTask = () => {
    if (currentTask.value.id) {
        axios.put(route('tasks.update', currentTask.value.id), currentTask.value).then(() => {
            getTasks();
        });
    } else {
        axios.post(route('tasks.store'), currentTask.value).then(() => {
            getTasks();
        });
    }

    currentTask.value = { ...newTask };
};

const editTask = (task: Task) => {
    currentTask.value = { ...task }; // Create a new object to avoid reference issues
    document.getElementById('task-form')?.scrollIntoView({ behavior: 'smooth' });
};

const deleteTask = (task: Task) => {
    axios.delete(route('tasks.destroy', task.id)).then(() => {
        getTasks();
    });
};

const completeTask = (task: Task) => {
    axios.post(route('tasks.complete', task.id)).then(() => {
        getTasks();
    });
};

const cancelTask = () => {
    currentTask.value = { ...newTask };
};

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs" @link-clicked="handleNavLinkClicked">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="task in sortedTasks" :key="task.id"
                    class="group hover:shadow-lg transition-all duration-200">
                    <CardHeader class="border-b flex items-center justify-center">
                        <CardTitle class="text-lg font-semibold">{{ task.title }}</CardTitle>
                    </CardHeader>
                    <CardContent class="flex-grow">
                        <p class="text-muted-foreground">{{ task.description }}</p>
                    </CardContent>
                    <CardFooter class="pt-2">
                        <div class="flex justify-end w-full gap-2">
                            <Button variant="ghost" v-if="task.deleted_at === null && task.completed_at === null"
                                size="sm" class="opacity-0 group-hover:opacity-100 transition-opacity"
                                @click="editTask(task)">
                                Edit
                            </Button>

                            <Button variant="default" v-if="task.deleted_at === null && task.completed_at === null"
                                size="sm" class="opacity-0 group-hover:opacity-100 transition-opacity"
                                @click="completeTask(task)">
                                Complete
                            </Button>

                            <Button variant="destructive" v-if="task.deleted_at === null" size="sm"
                                class="opacity-0 group-hover:opacity-100 transition-opacity" @click="deleteTask(task)">
                                Delete
                            </Button>
                        </div>
                    </CardFooter>
                </Card>
            </div>

            <Card v-if="tasks.length === 0" class="border-dashed">
                <CardContent class="flex flex-col items-center justify-center py-8">
                    <AlertCircle class="w-12 h-12 text-muted-foreground mb-4" />
                    <span class="text-lg text-muted-foreground">No tasks found</span>
                </CardContent>
            </Card>

            <Card class="border-primary/20" v-if="clickedNavItem?.href.includes('pending')" id="task-form">
                <CardHeader>
                    <CardTitle class="text-xl">{{ currentTask.id ? 'Edit' : 'Add New' }} Task</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="flex flex-col gap-4 w-full">
                        <Input hidden v-model="currentTask.id" />

                        <Input required class="w-full" v-model="currentTask.title" placeholder="Enter task title" />
                        <Input required class="w-full" v-model="currentTask.description"
                            placeholder="Enter task description" />

                    </form>
                </CardContent>

                <CardFooter>
                    <Button class="w-full" type="button" size="lg" @click="processTask">
                        {{ currentTask.id ? 'Update' : 'Add' }} Task
                    </Button>

                    <Button class="w-full" type="button" size="lg" variant="outline" @click="cancelTask"
                        v-if="currentTask.id">
                        Cancel
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template>
