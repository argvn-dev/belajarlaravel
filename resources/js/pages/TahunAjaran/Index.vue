<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import Input from '@/components/ui/input/Input.vue';
import { dashboard } from '@/routes';
import tahunAjaran from '@/routes/tahun-ajaran';

type TahunAjaranItem = {
    id: number;
    nama: string;
    aktif: boolean;
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Tahun Ajaran', href: tahunAjaran.index() },
        ],
    },
});

const props = defineProps<{ tahunAjaran: TahunAjaranItem[] }>();

const isOpen = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({ nama: '', aktif: false });

const openCreate = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    isOpen.value = true;
};

const openEdit = (item: TahunAjaranItem) => {
    editingId.value = item.id;
    form.nama = item.nama;
    form.aktif = item.aktif;
    form.clearErrors();
    isOpen.value = true;
};

const submit = () => {
    if (editingId.value) {
        form.put(tahunAjaran.update(editingId.value).url, {
            onSuccess: () => (isOpen.value = false),
        });

        return;
    }

    form.post(tahunAjaran.store().url, {
        onSuccess: () => (isOpen.value = false),
    });
};

const remove = (item: TahunAjaranItem) => {
    if (confirm(`Hapus tahun ajaran ${item.nama}?`)) {
        router.delete(tahunAjaran.destroy(item.id).url);
    }
};
</script>

<template>
    <Head title="Tahun Ajaran" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="hidden items-center justify-between gap-4 md:flex">
            <div>
                <h1 class="text-2xl font-semibold">Tahun Ajaran</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Kelola tahun ajaran sekolah.
                </p>
            </div>
            <Button @click="openCreate"><Plus class="size-4" /> Tambah</Button>
        </div>

        <Button
            class="fixed bottom-20 right-4 z-50 size-14 rounded-full shadow-lg md:hidden"
            :aria-label="'Tambah tahun ajaran'"
            @click="openCreate"
        >
            <Plus class="size-6" />
        </Button>

        <div
            class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-background dark:border-sidebar-border"
        >
            <table class="w-full text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-5 py-3 font-medium">Tahun Ajaran</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="w-28 px-5 py-3 text-right font-medium">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in props.tahunAjaran"
                        :key="item.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-5 py-4 font-medium">{{ item.nama }}</td>
                        <td class="px-5 py-4">
                            <span
                                :class="
                                    item.aktif
                                        ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                        : 'bg-muted text-muted-foreground'
                                "
                                class="rounded-full px-2.5 py-1 text-xs font-medium"
                            >
                                {{ item.aktif ? 'Aktif' : 'Tidak aktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-1">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    :aria-label="`Ubah ${item.nama}`"
                                    @click="openEdit(item)"
                                    ><Pencil class="size-4"
                                /></Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    :aria-label="`Hapus ${item.nama}`"
                                    @click="remove(item)"
                                    ><Trash2 class="size-4 text-destructive"
                                /></Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="props.tahunAjaran.length === 0">
                        <td
                            colspan="3"
                            class="px-5 py-12 text-center text-muted-foreground"
                        >
                            Belum ada tahun ajaran.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <Dialog v-model:open="isOpen">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{
                    editingId ? 'Ubah Tahun Ajaran' : 'Tambah Tahun Ajaran'
                }}</DialogTitle>
                <DialogDescription
                    >Masukkan nama dan status tahun ajaran.</DialogDescription
                >
            </DialogHeader>
            <form class="space-y-5" @submit.prevent="submit">
                <div class="space-y-2">
                    <label for="nama" class="text-sm font-medium">Nama</label>
                    <Input
                        id="nama"
                        v-model="form.nama"
                        placeholder="Contoh: 2025/2026"
                        autofocus
                    />
                    <p v-if="form.errors.nama" class="text-sm text-destructive">
                        {{ form.errors.nama }}
                    </p>
                </div>
                <label class="flex items-center gap-2 text-sm font-medium">
                    <Checkbox
                        :model-value="form.aktif"
                        @update:model-value="form.aktif = Boolean($event)"
                    />
                    Jadikan tahun ajaran aktif
                </label>
                <DialogFooter>
                    <Button type="submit" :disabled="form.processing"
                        >Simpan</Button
                    >
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
