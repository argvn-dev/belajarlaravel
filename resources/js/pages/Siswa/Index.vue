<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
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
import siswa from '@/routes/siswa';

type Placement = { kelas: { nama: string }; tahun_ajaran: { nama: string } };
type SiswaItem = {
    id: number;
    nis: string;
    nama: string;
    status_aktif: boolean;
    kelas_siswa: Placement[];
};
type Option = { id: number; nama: string };

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Siswa', href: siswa.index() },
        ],
    },
});

const props = defineProps<{
    siswa: SiswaItem[];
    kelas: Option[];
    tahunAjaran: Option[];
}>();
const isOpen = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({
    nis: '',
    nama: '',
    status_aktif: true,
    kelas_id: '',
    tahun_ajaran_id: '',
});
const currentPlacement = computed(
    () =>
        props.siswa.find((item) => item.id === editingId.value)?.kelas_siswa[0],
);

const openCreate = () => {
    editingId.value = null;
    form.reset();
    form.status_aktif = true;
    form.kelas_id = props.kelas[0]?.id.toString() ?? '';
    form.tahun_ajaran_id = props.tahunAjaran[0]?.id.toString() ?? '';
    form.clearErrors();
    isOpen.value = true;
};

const openEdit = (item: SiswaItem) => {
    editingId.value = item.id;
    form.nis = item.nis;
    form.nama = item.nama;
    form.status_aktif = item.status_aktif;
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
        form.put(siswa.update(editingId.value).url, options);

        return;
    }

    form.post(siswa.store().url, options);
};

const remove = (item: SiswaItem) => {
    if (confirm(`Hapus siswa ${item.nama}?`)) {
router.delete(siswa.destroy(item.id).url);
}
};
</script>

<template>
    <Head title="Siswa" />
    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Siswa</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Kelola data siswa dan kelasnya.
                </p>
            </div>
            <Button @click="openCreate"><Plus class="size-4" /> Tambah</Button>
        </div>
        <div
            class="hidden overflow-x-auto rounded-xl border border-sidebar-border/70 bg-background dark:border-sidebar-border md:block"
        >
            <table class="w-full min-w-175 text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-5 py-3 font-medium">NIS</th>
                        <th class="px-5 py-3 font-medium">Nama</th>
                        <th class="px-5 py-3 font-medium">Kelas</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="w-28 px-5 py-3 text-right font-medium">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in props.siswa"
                        :key="item.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-5 py-4">{{ item.nis }}</td>
                        <td class="px-5 py-4 font-medium">{{ item.nama }}</td>
                        <td class="px-5 py-4 text-muted-foreground">
                            {{ item.kelas_siswa[0]?.kelas.nama || '-' }}
                        </td>
                        <td class="px-5 py-4">
                            <span
                                :class="
                                    item.status_aktif
                                        ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                        : 'bg-muted text-muted-foreground'
                                "
                                class="rounded-full px-2.5 py-1 text-xs font-medium"
                                >{{
                                    item.status_aktif ? 'Aktif' : 'Tidak aktif'
                                }}</span
                            >
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
                    <tr v-if="props.siswa.length === 0">
                        <td
                            colspan="5"
                            class="px-5 py-12 text-center text-muted-foreground"
                        >
                            Belum ada siswa.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="space-y-3 md:hidden">
            <div
                v-for="item in props.siswa"
                :key="item.id"
                class="rounded-xl border border-sidebar-border/70 bg-background p-4 shadow-sm dark:border-sidebar-border"
            >
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="truncate font-medium">{{ item.nama }}</p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            NIS: {{ item.nis }}
                        </p>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Kelas: {{ item.kelas_siswa[0]?.kelas.nama || '-' }}
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-2">
                        <span
                            :class="
                                item.status_aktif
                                    ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                    : 'bg-muted text-muted-foreground'
                            "
                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                            >{{
                                item.status_aktif ? 'Aktif' : 'Tidak aktif'
                            }}</span
                        >
                        <div class="flex gap-1">
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
            </div>
            <p
                v-if="props.siswa.length === 0"
                class="py-12 text-center text-sm text-muted-foreground"
            >
                Belum ada siswa.
            </p>
        </div>
    </div>
    <Dialog v-model:open="isOpen"
        ><DialogContent
            ><DialogHeader
                ><DialogTitle>{{
                    editingId ? 'Ubah Siswa' : 'Tambah Siswa'
                }}</DialogTitle
                ><DialogDescription
                    >Masukkan data siswa dan penempatan
                    kelas.</DialogDescription
                ></DialogHeader
            >
            <form class="space-y-4" @submit.prevent="submit">
                <label class="block space-y-2 text-sm font-medium"
                    >NIS<Input v-model="form.nis" autofocus /><span
                        v-if="form.errors.nis"
                        class="text-destructive"
                        >{{ form.errors.nis }}</span
                    ></label
                ><label class="block space-y-2 text-sm font-medium"
                    >Nama<Input v-model="form.nama" /><span
                        v-if="form.errors.nama"
                        class="text-destructive"
                        >{{ form.errors.nama }}</span
                    ></label
                ><label class="block space-y-2 text-sm font-medium"
                    >Kelas<select
                        v-model="form.kelas_id"
                        class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                    >
                        <option value="" disabled>Pilih kelas</option>
                        <option
                            v-for="item in props.kelas"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.nama }}
                        </option></select
                    ><span
                        v-if="form.errors.kelas_id"
                        class="text-destructive"
                        >{{ form.errors.kelas_id }}</span
                    ></label
                ><label class="block space-y-2 text-sm font-medium"
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
                ><label class="flex items-center gap-2 text-sm font-medium"
                    ><Checkbox
                        :model-value="form.status_aktif"
                        @update:model-value="
                            form.status_aktif = Boolean($event)
                        "
                    />Siswa aktif</label
                ><DialogFooter
                    ><Button type="submit" :disabled="form.processing"
                        >Simpan</Button
                    ></DialogFooter
                >
            </form></DialogContent
        ></Dialog
    >
</template>
