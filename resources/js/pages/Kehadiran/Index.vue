<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import Button from '@/components/ui/button/Button.vue';
import { dashboard } from '@/routes';
import kehadiranRoutes from '@/routes/kehadiran';

type KelasOption = { id: number; nama: string; jurusan: string | null; tingkat: string | null };
type GuruOption = { id: number; nama: string; mapel: string | null };
type JadwalOption = {
    id: number;
    hari: string;
    jam_mulai: string;
    jam_selesai: string;
    kelas: KelasOption;
    guru: GuruOption;
};
type KehadiranItem = {
    id: number;
    tanggal: string;
    keterangan: string | null;
    jadwal_pelajaran: JadwalOption;
    jumlah_hadir: number;
    sakit: number;
    izin: number;
    alpa: number;
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Kehadiran', href: kehadiranRoutes.index() },
        ],
    },
});

defineProps<{
    kehadiran: KehadiranItem[];
}>();

const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
const formatTanggal = (value: string): string => {
    const date = new Date(value);

    return `${date.getDate()} ${bulan[date.getMonth()]} ${date.getFullYear()}`;
};

const remove = (item: KehadiranItem) => {
    if (confirm(`Hapus catatan kehadiran tanggal ${item.tanggal}?`)) {
        router.delete(kehadiranRoutes.destroy(item.id).url);
    }
};
</script>

<template>
    <Head title="Kehadiran" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">Kehadiran</h1>
                <p class="mt-1 text-sm text-muted-foreground">Daftar catatan kehadiran siswa per pertemuan.</p>
            </div>
            <Button @click="router.visit(kehadiranRoutes.create().url)"><Plus class="size-4" /> Tambah</Button>
        </div>

        <div class="hidden overflow-x-auto rounded-xl border border-sidebar-border/70 bg-background dark:border-sidebar-border md:block">
            <table class="w-full min-w-230 text-sm">
                <thead class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border">
                    <tr>
                        <th class="px-5 py-3 font-medium">Tanggal</th>
                        <th class="px-5 py-3 font-medium">Jadwal Pelajaran</th>
                        <th class="px-5 py-3 font-medium">Kelas</th>
                        <th class="px-5 py-3 font-medium">Guru</th>
                        <th class="px-3 py-3 text-center font-medium">Hadir</th>
                        <th class="px-3 py-3 text-center font-medium">Sakit</th>
                        <th class="px-3 py-3 text-center font-medium">Izin</th>
                        <th class="px-3 py-3 text-center font-medium">Alpa</th>
                        <th class="w-28 px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in kehadiran" :key="item.id" class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border">
                        <td class="px-5 py-4 font-medium">{{ formatTanggal(item.tanggal) }}</td>
                        <td class="px-5 py-4">
                            <div>{{ item.jadwal_pelajaran.guru.mapel ?? '—' }}</div>
                            <div class="text-xs text-muted-foreground">
                                {{ item.jadwal_pelajaran.hari }} · {{ item.jadwal_pelajaran.jam_mulai.slice(0, 5) }} - {{ item.jadwal_pelajaran.jam_selesai.slice(0, 5) }}
                            </div>
                        </td>
                        <td class="px-5 py-4">{{ item.jadwal_pelajaran.kelas.nama }}</td>
                        <td class="px-5 py-4">{{ item.jadwal_pelajaran.guru.nama }}</td>
                        <td class="px-3 py-4 text-center">{{ item.jumlah_hadir }}</td>
                        <td class="px-3 py-4 text-center">{{ item.sakit }}</td>
                        <td class="px-3 py-4 text-center">{{ item.izin }}</td>
                        <td class="px-3 py-4 text-center">{{ item.alpa }}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-1">
                                <Button variant="ghost" size="icon" :aria-label="`Ubah kehadiran ${item.tanggal}`" @click="router.visit(kehadiranRoutes.edit(item.id).url)"><Pencil class="size-4" /></Button>
                                <Button variant="ghost" size="icon" :aria-label="`Hapus kehadiran ${item.tanggal}`" @click="remove(item)"><Trash2 class="size-4 text-destructive" /></Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="kehadiran.length === 0">
                        <td colspan="9" class="px-5 py-12 text-center text-muted-foreground">Belum ada catatan kehadiran.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="space-y-3 md:hidden">
            <div
                v-for="item in kehadiran"
                :key="item.id"
                class="rounded-xl border border-sidebar-border/70 bg-background p-4 shadow-sm dark:border-sidebar-border"
            >
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-medium">{{ formatTanggal(item.tanggal) }}</p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            {{ item.jadwal_pelajaran.guru.mapel ?? '—' }} ·
                            {{ item.jadwal_pelajaran.hari }} · {{ item.jadwal_pelajaran.jam_mulai.slice(0, 5) }} - {{ item.jadwal_pelajaran.jam_selesai.slice(0, 5) }}
                        </p>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            {{ item.jadwal_pelajaran.kelas.nama }} · {{ item.jadwal_pelajaran.guru.nama }}
                        </p>
                    </div>
                    <div class="flex shrink-0 gap-1">
                        <Button variant="ghost" size="icon" :aria-label="`Ubah kehadiran ${item.tanggal}`" @click="router.visit(kehadiranRoutes.edit(item.id).url)"><Pencil class="size-4" /></Button>
                        <Button variant="ghost" size="icon" :aria-label="`Hapus kehadiran ${item.tanggal}`" @click="remove(item)"><Trash2 class="size-4 text-destructive" /></Button>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-4 gap-2 text-center">
                    <div class="rounded-lg bg-muted/50 py-2">
                        <p class="text-sm font-semibold">{{ item.jumlah_hadir }}</p>
                        <p class="text-[10px] text-muted-foreground">Hadir</p>
                    </div>
                    <div class="rounded-lg bg-muted/50 py-2">
                        <p class="text-sm font-semibold">{{ item.sakit }}</p>
                        <p class="text-[10px] text-muted-foreground">Sakit</p>
                    </div>
                    <div class="rounded-lg bg-muted/50 py-2">
                        <p class="text-sm font-semibold">{{ item.izin }}</p>
                        <p class="text-[10px] text-muted-foreground">Izin</p>
                    </div>
                    <div class="rounded-lg bg-muted/50 py-2">
                        <p class="text-sm font-semibold">{{ item.alpa }}</p>
                        <p class="text-[10px] text-muted-foreground">Alpa</p>
                    </div>
                </div>
            </div>
            <p
                v-if="kehadiran.length === 0"
                class="py-12 text-center text-sm text-muted-foreground"
            >
                Belum ada catatan kehadiran.
            </p>
        </div>
    </div>
</template>
