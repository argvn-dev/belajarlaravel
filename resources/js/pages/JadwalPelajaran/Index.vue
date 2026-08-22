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
import { dashboard } from '@/routes';
import jadwalPelajaran from '@/routes/jadwal-pelajaran';

type Option = { id: number; nama: string };
type KelasOption = Option & { jurusan: string; tingkat: string };
type GuruOption = Option & { mapel: string | null };
type JadwalItem = {
    id: number;
    tahun_ajaran_id: number;
    kelas_id: number;
    guru_id: number;
    hari: string;
    jam_mulai: string;
    jam_selesai: string;
    tahun_ajaran: Option;
    kelas: KelasOption;
    guru: GuruOption;
};

const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Jadwal Pelajaran', href: jadwalPelajaran.index() },
        ],
    },
});

const props = defineProps<{
    jadwalPelajaran: JadwalItem[];
    tahunAjaran: Option[];
    kelas: KelasOption[];
    guru: GuruOption[];
}>();

const isOpen = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({
    tahun_ajaran_id: '',
    kelas_id: '',
    guru_id: '',
    hari: 'Senin',
    jam_mulai: '',
    jam_selesai: '',
});

const openCreate = () => {
    editingId.value = null;
    form.reset();
    form.hari = 'Senin';
    form.clearErrors();
    isOpen.value = true;
};

const openEdit = (item: JadwalItem) => {
    editingId.value = item.id;
    form.tahun_ajaran_id = item.tahun_ajaran_id.toString();
    form.kelas_id = item.kelas_id.toString();
    form.guru_id = item.guru_id.toString();
    form.hari = item.hari;
    form.jam_mulai = item.jam_mulai.slice(0, 5);
    form.jam_selesai = item.jam_selesai.slice(0, 5);
    form.clearErrors();
    isOpen.value = true;
};

const submit = () => {
    const options = { onSuccess: () => (isOpen.value = false) };

    if (editingId.value) {
        form.put(jadwalPelajaran.update(editingId.value).url, options);

        return;
    }

    form.post(jadwalPelajaran.store().url, options);
};

const remove = (item: JadwalItem) => {
    if (confirm(`Hapus jadwal ${item.hari} ${item.jam_mulai.slice(0, 5)}?`)) {
        router.delete(jadwalPelajaran.destroy(item.id).url);
    }
};
</script>

<template>
    <Head title="Jadwal Pelajaran" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Jadwal Pelajaran</h1>
                <p class="mt-1 text-sm text-muted-foreground">Kelola jadwal mengajar setiap kelas.</p>
            </div>
            <Button @click="openCreate"><Plus class="size-4" /> Tambah</Button>
        </div>

        <div class="hidden overflow-x-auto rounded-xl border border-sidebar-border/70 bg-background dark:border-sidebar-border md:block">
            <table class="w-full min-w-225 text-sm">
                <thead class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border">
                    <tr>
                        <th class="px-5 py-3 font-medium">Hari</th>
                        <th class="px-5 py-3 font-medium">Waktu</th>
                        <th class="px-5 py-3 font-medium">Kelas</th>
                        <th class="px-5 py-3 font-medium">Guru</th>
                        <th class="px-5 py-3 font-medium">Tahun Ajaran</th>
                        <th class="w-28 px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in props.jadwalPelajaran" :key="item.id" class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border">
                        <td class="px-5 py-4 font-medium">{{ item.hari }}</td>
                        <td class="px-5 py-4 text-muted-foreground">{{ item.jam_mulai.slice(0, 5) }} - {{ item.jam_selesai.slice(0, 5) }}</td>
                        <td class="px-5 py-4">{{ item.kelas.nama }}</td>
                        <td class="px-5 py-4">{{ item.guru.nama }}</td>
                        <td class="px-5 py-4 text-muted-foreground">{{ item.tahun_ajaran.nama }}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-1">
                                <Button variant="ghost" size="icon" :aria-label="`Ubah jadwal ${item.hari}`" @click="openEdit(item)"><Pencil class="size-4" /></Button>
                                <Button variant="ghost" size="icon" :aria-label="`Hapus jadwal ${item.hari}`" @click="remove(item)"><Trash2 class="size-4 text-destructive" /></Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="props.jadwalPelajaran.length === 0">
                        <td colspan="6" class="px-5 py-12 text-center text-muted-foreground">Belum ada jadwal pelajaran.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="space-y-3 md:hidden">
            <div
                v-for="item in props.jadwalPelajaran"
                :key="item.id"
                class="rounded-xl border border-sidebar-border/70 bg-background p-4 shadow-sm dark:border-sidebar-border"
            >
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-medium">{{ item.hari }}</p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            {{ item.jam_mulai.slice(0, 5) }} - {{ item.jam_selesai.slice(0, 5) }}
                        </p>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Kelas: {{ item.kelas.nama }}
                        </p>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Guru: {{ item.guru.nama }}
                        </p>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            {{ item.tahun_ajaran.nama }}
                        </p>
                    </div>
                    <div class="flex shrink-0 gap-1">
                        <Button variant="ghost" size="icon" :aria-label="`Ubah jadwal ${item.hari}`" @click="openEdit(item)"><Pencil class="size-4" /></Button>
                        <Button variant="ghost" size="icon" :aria-label="`Hapus jadwal ${item.hari}`" @click="remove(item)"><Trash2 class="size-4 text-destructive" /></Button>
                    </div>
                </div>
            </div>
            <p
                v-if="props.jadwalPelajaran.length === 0"
                class="py-12 text-center text-sm text-muted-foreground"
            >
                Belum ada jadwal pelajaran.
            </p>
        </div>
    </div>

    <Dialog v-model:open="isOpen">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ editingId ? 'Ubah Jadwal Pelajaran' : 'Tambah Jadwal Pelajaran' }}</DialogTitle>
                <DialogDescription>Masukkan data jadwal mengajar.</DialogDescription>
            </DialogHeader>
            <form class="space-y-5" @submit.prevent="submit">
                <label class="block space-y-2 text-sm font-medium">Tahun Ajaran
                    <select v-model="form.tahun_ajaran_id" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm">
                        <option value="" disabled>Pilih tahun ajaran</option>
                        <option v-for="item in props.tahunAjaran" :key="item.id" :value="item.id">{{ item.nama }}</option>
                    </select>
                    <span v-if="form.errors.tahun_ajaran_id" class="text-destructive">{{ form.errors.tahun_ajaran_id }}</span>
                </label>
                <label class="block space-y-2 text-sm font-medium">Kelas
                    <select v-model="form.kelas_id" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm">
                        <option value="" disabled>Pilih kelas</option>
                        <option v-for="item in props.kelas" :key="item.id" :value="item.id">{{ item.nama }} - {{ item.jurusan }}</option>
                    </select>
                    <span v-if="form.errors.kelas_id" class="text-destructive">{{ form.errors.kelas_id }}</span>
                </label>
                <label class="block space-y-2 text-sm font-medium">Guru
                    <select v-model="form.guru_id" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm">
                        <option value="" disabled>Pilih guru</option>
                        <option v-for="item in props.guru" :key="item.id" :value="item.id">{{ item.nama }}{{ item.mapel ? ` - ${item.mapel}` : '' }}</option>
                    </select>
                    <span v-if="form.errors.guru_id" class="text-destructive">{{ form.errors.guru_id }}</span>
                </label>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <label class="block space-y-2 text-sm font-medium">Hari
                        <select v-model="form.hari" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm">
                            <option v-for="day in days" :key="day" :value="day">{{ day }}</option>
                        </select>
                        <span v-if="form.errors.hari" class="text-destructive">{{ form.errors.hari }}</span>
                    </label>
                    <label class="block space-y-2 text-sm font-medium">Mulai
                        <input v-model="form.jam_mulai" type="time" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm" />
                        <span v-if="form.errors.jam_mulai" class="text-destructive">{{ form.errors.jam_mulai }}</span>
                    </label>
                    <label class="block space-y-2 text-sm font-medium">Selesai
                        <input v-model="form.jam_selesai" type="time" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm" />
                        <span v-if="form.errors.jam_selesai" class="text-destructive">{{ form.errors.jam_selesai }}</span>
                    </label>
                </div>
                <DialogFooter><Button type="submit" :disabled="form.processing">Simpan</Button></DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
