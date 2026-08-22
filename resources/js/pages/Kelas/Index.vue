<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
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
import kelas from '@/routes/kelas';

type KelasItem = {
    id: number;
    nama: string;
    jurusan: string;
    tingkat: string;
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Kelas', href: kelas.index() },
        ],
    },
});

const props = defineProps<{ kelas: KelasItem[] }>();
const isOpen = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({ nama: '', jurusan: '', tingkat: '' });

const openCreate = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    isOpen.value = true;
};

const openEdit = (item: KelasItem) => {
    editingId.value = item.id;
    form.nama = item.nama;
    form.jurusan = item.jurusan;
    form.tingkat = item.tingkat;
    form.clearErrors();
    isOpen.value = true;
};

const submit = () => {
    if (editingId.value) {
        form.put(kelas.update(editingId.value).url, {
            onSuccess: () => (isOpen.value = false),
        });

        return;
    }

    form.post(kelas.store().url, { onSuccess: () => (isOpen.value = false) });
};

const remove = (item: KelasItem) => {
    if (confirm(`Hapus kelas ${item.nama}?`)) {
        router.delete(kelas.destroy(item.id).url);
    }
};
</script>

<template>
    <Head title="Kelas" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Kelas</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Kelola data kelas sekolah.
                </p>
            </div>
            <Button @click="openCreate"><Plus class="size-4" /> Tambah</Button>
        </div>

        <div
            class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-background dark:border-sidebar-border"
        >
            <table class="w-full text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-5 py-3 font-medium">Nama</th>
                        <th class="hidden px-5 py-3 font-medium md:table-cell">
                            Jurusan
                        </th>
                        <th class="hidden px-5 py-3 font-medium md:table-cell">
                            Tingkat
                        </th>
                        <th class="w-28 px-5 py-3 text-right font-medium">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in props.kelas"
                        :key="item.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-5 py-4 font-medium">{{ item.nama }}</td>
                        <td class="hidden px-5 py-4 md:table-cell">
                            {{ item.jurusan }}
                        </td>
                        <td class="hidden px-5 py-4 md:table-cell">
                            {{ item.tingkat }}
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
                    <tr v-if="props.kelas.length === 0">
                        <td
                            colspan="4"
                            class="px-5 py-12 text-center text-muted-foreground"
                        >
                            Belum ada kelas.
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
                    editingId ? 'Ubah Kelas' : 'Tambah Kelas'
                }}</DialogTitle>
                <DialogDescription>Masukkan detail kelas.</DialogDescription>
            </DialogHeader>
            <form class="space-y-5" @submit.prevent="submit">
                <label class="block space-y-2 text-sm font-medium"
                    >Nama<Input
                        v-model="form.nama"
                        placeholder="Contoh: X IPA 1"
                        autofocus
                    /><span v-if="form.errors.nama" class="text-destructive">{{
                        form.errors.nama
                    }}</span></label
                >
                <label class="block space-y-2 text-sm font-medium"
                    >Jurusan<Input
                        v-model="form.jurusan"
                        placeholder="Contoh: IPA"
                    /><span
                        v-if="form.errors.jurusan"
                        class="text-destructive"
                        >{{ form.errors.jurusan }}</span
                    ></label
                >
                <label class="block space-y-2 text-sm font-medium"
                    >Tingkat<Input
                        v-model="form.tingkat"
                        placeholder="Contoh: X"
                    /><span
                        v-if="form.errors.tingkat"
                        class="text-destructive"
                        >{{ form.errors.tingkat }}</span
                    ></label
                >
                <DialogFooter
                    ><Button type="submit" :disabled="form.processing"
                        >Simpan</Button
                    ></DialogFooter
                >
            </form>
        </DialogContent>
    </Dialog>
</template>
