<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { dashboard } from '@/routes';
import kehadiranRoutes from '@/routes/kehadiran';

type SiswaOption = { id: number; nis: string | null; nama: string };
type KehadiranSiswaItem = { id: number; status: string; siswa: SiswaOption };
type JadwalOption = {
    hari: string;
    jam_mulai: string;
    jam_selesai: string;
    kelas: { nama: string };
    guru: { nama: string; mapel: string | null };
};
type KehadiranItem = {
    id: number;
    tanggal: string;
    keterangan: string | null;
    jadwal_pelajaran: JadwalOption;
    kehadiran_siswa: KehadiranSiswaItem[];
};

const props = defineProps<{
    kehadiran: KehadiranItem;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Kehadiran', href: kehadiranRoutes.index() },
        ],
    },
});

const statusList = ['Hadir', 'Sakit', 'Izin', 'Alpa'];

const form = useForm({
    tanggal: props.kehadiran.tanggal,
    keterangan: props.kehadiran.keterangan ?? '',
    statuses: props.kehadiran.kehadiran_siswa.map((item) => ({
        id: item.id,
        status: item.status,
    })),
});

const submit = () => {
    form.put(kehadiranRoutes.update(props.kehadiran.id).url);
};

const routerBack = () => router.visit(kehadiranRoutes.index().url);
</script>

<template>
    <Head :title="`Ubah Kehadiran - ${kehadiran.tanggal}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <Button variant="ghost" size="sm" class="mb-2" @click="() => routerBack()">
                <ArrowLeft class="size-4" /> Kembali
            </Button>
            <h1 class="text-2xl font-semibold">Ubah Kehadiran</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                {{ kehadiran.jadwal_pelajaran.kelas.nama }} ·
                {{ kehadiran.jadwal_pelajaran.guru.nama }}
                <span v-if="kehadiran.jadwal_pelajaran.guru.mapel">· {{ kehadiran.jadwal_pelajaran.guru.mapel }}</span>
                · {{ kehadiran.jadwal_pelajaran.hari }}
                {{ kehadiran.jadwal_pelajaran.jam_mulai.slice(0, 5) }} - {{ kehadiran.jadwal_pelajaran.jam_selesai.slice(0, 5) }}
            </p>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="tanggal">Tanggal</Label>
                    <Input id="tanggal" v-model="form.tanggal" type="date" />
                    <span v-if="form.errors.tanggal" class="text-sm text-destructive">{{ form.errors.tanggal }}</span>
                </div>
            </div>

            <div class="space-y-2">
                <Label for="keterangan">Keterangan</Label>
                <textarea id="keterangan" v-model="form.keterangan" rows="3" class="h-auto w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"></textarea>
            </div>

            <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 bg-background dark:border-sidebar-border">
                <table class="w-full text-sm">
                    <thead class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border">
                        <tr>
                            <th class="px-5 py-3 font-medium">NIS</th>
                            <th class="px-5 py-3 font-medium">Nama Siswa</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in form.statuses" :key="item.id" class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border">
                            <td class="px-5 py-3 text-muted-foreground">{{ kehadiran.kehadiran_siswa[index].siswa.nis }}</td>
                            <td class="px-5 py-3 font-medium">{{ kehadiran.kehadiran_siswa[index].siswa.nama }}</td>
                            <td class="px-5 py-3">
                                <select v-model="item.status" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm">
                                    <option v-for="status in statusList" :key="status" :value="status">{{ status }}</option>
                                </select>
                            </td>
                        </tr>
                        <tr v-if="form.statuses.length === 0">
                            <td colspan="3" class="px-5 py-12 text-center text-muted-foreground">Belum ada siswa pada catatan ini.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">Simpan</Button>
            </div>
        </form>
    </div>
</template>
