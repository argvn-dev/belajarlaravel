<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
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
import guru from '@/routes/guru';

type Placement = { kelas: Option; tahun_ajaran: Option };
type GuruItem = {
    id: number;
    nama: string;
    mapel: string | null;
    wali_kelas: Placement[];
};
type Option = { id: number; nama: string };

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Guru', href: guru.index() },
        ],
    },
});

const props = defineProps<{
    guru: GuruItem[];
    kelas: Option[];
    tahunAjaran: Option[];
}>();
const isOpen = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({
    nama: '',
    mapel: '',
    kelas_id: '',
    tahun_ajaran_id: '',
});
const currentPlacement = computed(
    () => props.guru.find((item) => item.id === editingId.value)?.wali_kelas[0],
);

const openCreate = () => {
    editingId.value = null;
    form.reset();
    form.kelas_id = '';
    form.tahun_ajaran_id = '';
    form.clearErrors();
    isOpen.value = true;
};

const openEdit = (item: GuruItem) => {
    editingId.value = item.id;
    form.nama = item.nama;
    form.mapel = item.mapel ?? '';
    form.kelas_id = currentPlacement.value?.kelas
        ? (props.kelas
              .find(
                  (kelas) => kelas.nama === currentPlacement.value?.kelas.nama,
              )
              ?.id.toString() ?? '')
        : '';
    form.tahun_ajaran_id = currentPlacement.value?.tahun_ajaran
        ? (props.tahunAjaran
              .find(
                  (tahun) =>
                      tahun.nama === currentPlacement.value?.tahun_ajaran.nama,
              )
              ?.id.toString() ?? '')
        : '';
    form.clearErrors();
    isOpen.value = true;
};

const submit = () => {
    const options = { onSuccess: () => (isOpen.value = false) };

    if (editingId.value) {
        form.put(guru.update(editingId.value).url, options);

        return;
    }

    form.post(guru.store().url, options);
};

const remove = (item: GuruItem) => {
    if (confirm(`Hapus guru ${item.nama}?`)) {
        router.delete(guru.destroy(item.id).url);
    }
};
</script>

<template>
    <Head title="Guru" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Guru</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Kelola data guru sekolah.
                </p>
            </div>
            <Button @click="openCreate"><Plus class="size-4" /> Tambah</Button>
        </div>

        <div
            class="hidden overflow-hidden rounded-xl border border-sidebar-border/70 bg-background dark:border-sidebar-border md:block"
        >
            <table class="w-full text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-5 py-3 font-medium">Nama</th>
                        <th class="px-5 py-3 font-medium">Mata Pelajaran</th>
                        <th class="px-5 py-3 font-medium">Wali Kelas</th>
                        <th class="w-28 px-5 py-3 text-right font-medium">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in props.guru"
                        :key="item.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-5 py-4 font-medium">{{ item.nama }}</td>
                        <td class="px-5 py-4 text-muted-foreground">
                            {{ item.mapel || '-' }}
                        </td>
                        <td class="px-5 py-4 text-muted-foreground">
                            {{ item.wali_kelas[0]?.kelas.nama || '-' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-1">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    :aria-label="`Ubah ${item.nama}`"
                                    @click="openEdit(item)"
                                    ><Pencil class="size-4" /></Button
                                ><Button
                                    variant="ghost"
                                    size="icon"
                                    :aria-label="`Hapus ${item.nama}`"
                                    @click="remove(item)"
                                    ><Trash2 class="size-4 text-destructive"
                                /></Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="props.guru.length === 0">
                        <td
                            colspan="4"
                            class="px-5 py-12 text-center text-muted-foreground"
                        >
                            Belum ada guru.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="space-y-3 md:hidden">
            <div
                v-for="item in props.guru"
                :key="item.id"
                class="rounded-xl border border-sidebar-border/70 bg-background p-4 shadow-sm dark:border-sidebar-border"
            >
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="truncate font-medium">{{ item.nama }}</p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            {{ item.mapel || '-' }}
                        </p>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Wali:
                            {{ item.wali_kelas[0]?.kelas.nama || '-' }}
                        </p>
                    </div>
                    <div class="flex shrink-0 gap-1">
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
                </div>
            </div>
            <p
                v-if="props.guru.length === 0"
                class="py-12 text-center text-sm text-muted-foreground"
            >
                Belum ada guru.
            </p>
        </div>
    </div>

    <Dialog v-model:open="isOpen"
        ><DialogContent
            ><DialogHeader
                ><DialogTitle>{{
                    editingId ? 'Ubah Guru' : 'Tambah Guru'
                }}</DialogTitle
                ><DialogDescription
                    >Masukkan data guru.</DialogDescription
                ></DialogHeader
            >
            <form class="space-y-5" @submit.prevent="submit">
                <label class="block space-y-2 text-sm font-medium"
                    >Nama<Input
                        v-model="form.nama"
                        placeholder="Nama guru"
                        autofocus
                    /><span v-if="form.errors.nama" class="text-destructive">{{
                        form.errors.nama
                    }}</span></label
                ><label class="block space-y-2 text-sm font-medium"
                    >Mata Pelajaran<Input
                        v-model="form.mapel"
                        placeholder="Opsional"
                    /><span v-if="form.errors.mapel" class="text-destructive">{{
                        form.errors.mapel
                    }}</span></label
                ><label class="block space-y-2 text-sm font-medium"
                    >Wali Kelas<select
                        v-model="form.kelas_id"
                        class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                    >
                        <option value="">Bukan wali kelas</option>
                        <option
                            v-for="item in props.kelas"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.nama }}
                        </option>
                    </select></label
                ><label
                    v-if="form.kelas_id"
                    class="block space-y-2 text-sm font-medium"
                    >Tahun Ajaran<select
                        v-model="form.tahun_ajaran_id"
                        class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                    >
                        <option value="" disabled>Pilih tahun ajaran</option>
                        <option
                            v-for="item in props.tahunAjaran"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.nama }}
                        </option></select
                    ><span
                        v-if="form.errors.tahun_ajaran_id"
                        class="text-destructive"
                        >{{ form.errors.tahun_ajaran_id }}</span
                    ></label
                ><DialogFooter
                    ><Button type="submit" :disabled="form.processing"
                        >Simpan</Button
                    ></DialogFooter
                >
            </form></DialogContent
        ></Dialog
    >
</template>
