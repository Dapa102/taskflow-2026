import { ref } from "vue";
import { useTaskStore } from "@/stores/taskStore";

export function useTaskActions() {
    const taskStore = useTaskStore();
    const drawerOpen = ref(false);
    const editingTask = ref(null);

    function openCreate() {
        editingTask.value = null;
        drawerOpen.value = true;
    }

    function openEdit(task) {
        editingTask.value = { ...task };
        drawerOpen.value = true;
    }

    function closeDrawer() {
        drawerOpen.value = false;
        editingTask.value = null;
    }

    async function saveTask(data) {
        if (editingTask.value?.id) {
            await taskStore.updateTask(editingTask.value.id, data);
        } else {
            await taskStore.addTask(data);
        }
        closeDrawer();
    }

    return {
        drawerOpen,
        editingTask,
        openCreate,
        openEdit,
        closeDrawer,
        saveTask,
    };
}
