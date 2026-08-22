<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import { computed, reactive, watch } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { dashboard } from '@/routes';
import kehadiranRoutes from '@/routes/kehadiran';

type KelasOption = { id: number; nama: string; jurusan: string | null; tingkat: string | null };
type GuruOption = { id: number; nama: string; mapel: string | null };
type JadwalOption = { id: number; hari: string; jam_mulai: string; jam_selesai: string; guru: GuruOption };
type SiswaOption = { id: number; nis: string | null; nama: string };

const props = defineProps<{
    kelas: KelasOption[];
    tahunAjaran: { id: number; nama: string } | null;
    jadwalPelajaran: JadwalOption[];
    siswa: SiswaOption[];
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
const today = new Date().toISOString().slice(0, 10);

const selectedHari = computed(() => {
    if (!form.tanggal) {
        return '';
    }

    return new Date(`${form.tanggal}T00:00:00`).toLocaleDateString('id-ID', { weekday: 'long' });
});

const jadwalTersedia = computed(() =>
    props.jadwalPelajaran.filter((item) => item.hari === selectedHari.value),
);

watch(selectedHari, () => {
    if (!jadwalTersedia.value.some((item) => item.id === Number(form.jadwal_pelajaran_id))) {
        form.jadwal_pelajaran_id = '';
    }
});

const form = useForm({
    tanggal: today,
    kelas_id: '',
    jadwal_pelajaran_id: '',
    keterangan: '',
    statuses: [] as { siswa_id: number; status: string }[],
});

const statuses = reactive<Record<number, string>>({});

watch(
    () => props.siswa,
    (list) => {
        for (const siswa of list) {
            if (!(siswa.id in statuses)) {
                statuses[siswa.id] = 'Hadir';
            }
        }
    },
    { immediate: true },
);

const onKelasChange = () => {
    form.jadwal_pelajaran_id = '';

    for (const key of Object.keys(statuses)) {
        delete statuses[Number(key)];
    }

    router.get(
        kehadiranRoutes.create().url,
        { kelas_id: form.kelas_id },
        { preserveState: true, replace: true, only: ['jadwalPelajaran', 'siswa'] },
    );
};

const routerBack = () => router.visit(kehadiranRoutes.index().url);

const submit = () => {
    form.statuses = props.siswa.map((siswa) => ({
        siswa_id: siswa.id,
        status: statuses[siswa.id] ?? 'Hadir',
    }));
    form.post(kehadiranRoutes.store().url);
};
</script>

<template>
    <Head title="Tambah Kehadiran" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <Button variant="ghost" size="sm" class="mb-2" @click="routerBack">
                <ArrowLeft class="size-4" /> Kembali
            </Button>
            <h1 class="text-2xl font-semibold">Tambah Kehadiran</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Catat kehadiran siswa untuk satu pertemuan pelajaran.
                <span v-if="props.tahunAjaran"> Tahun ajaran: {{ props.tahunAjaran.nama }}.</span>
            </p>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="space-y-2">
                    <Label for="tanggal">Tanggal</Label>
                    <Input id="tanggal" v-model="form.tanggal" type="date" />
                    <span v-if="form.errors.tanggal" class="text-sm text-destructive">{{ form.errors.tanggal }}</span>
                </div>
                <div class="space-y-2">
                    <Label for="kelas">Kelas</Label>
                    <select id="kelas" v-model="form.kelas_id" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm" @change="onKelasChange">
                        <option value="" disabled>Pilih kelas</option>
                        <option v-for="item in props.kelas" :key="item.id" :value="item.id">
                            {{ item.nama }}<span v-if="item.jurusan"> - {{ item.jurusan }}</span>
                        </option>
                    </select>
                    <span v-if="form.errors.kelas_id" class="text-sm text-destructive">{{ form.errors.kelas_id }}</span>
                </div>
                <div class="space-y-2">
                    <Label for="jadwal_pelajaran_id">Jadwal Pelajaran</Label>
                    <select id="jadwal_pelajaran_id" v-model="form.jadwal_pelajaran_id" class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm">
                        <option value="" disabled>Pilih jadwal</option>
                        <option v-for="item in jadwalTersedia" :key="item.id" :value="item.id">
                            {{ item.guru.mapel ?? 'Tanpa Mapel' }} · {{ item.hari }} {{ item.jam_mulai.slice(0, 5) }} - {{ item.jam_selesai.slice(0, 5) }}
                        </option>
                    </select>
                    <span v-if="form.errors.jadwal_pelajaran_id" class="text-sm text-destructive">{{ form.errors.jadwal_pelajaran_id }}</span>
                    <span v-if="form.kelas_id && props.jadwalPelajaran.length === 0" class="text-xs text-muted-foreground">Tidak ada jadwal untuk kelas ini.</span>
                    <span v-else-if="form.kelas_id && jadwalTersedia.length === 0" class="text-xs text-muted-foreground">Tidak ada jadwal pada hari {{ selectedHari }}.</span>
                </div>
            </div>

            <div class="space-y-2">
                <Label for="keterangan">Keterangan</Label>
                <textarea id="keterangan" v-model="form.keterangan" rows="2" class="h-auto w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"></textarea>
            </div>

            <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 bg-background dark:border-sidebar-border">
                <table class="w-full text-sm">
                    <thead class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border">
                        <tr>
                            <th class="px-5 py-3 font-medium">NIS</th>
                            <th class="px-5 py-3 font-medium">Nama Siswa</th>
                            <th class="px-5 py-3 text-center font-medium">Hadir</th>
                            <th class="px-5 py-3 text-center font-medium">Sakit</th>
                            <th class="px-5 py-3 text-center font-medium">Izin</th>
                            <th class="px-5 py-3 text-center font-medium">Alpa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="siswa in props.siswa" :key="siswa.id" class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border">
                            <td class="px-5 py-3 text-muted-foreground">{{ siswa.nis }}</td>
                            <td class="px-5 py-3 font-medium">{{ siswa.nama }}</td>
                            <td v-for="status in statusList" :key="status" class="px-5 py-3 text-center">
                                <input
                                    type="checkbox"
                                    class="size-4 cursor-pointer accent-primary"
                                    :checked="statuses[siswa.id] === status"
                                    :aria-label="`${status} - ${siswa.nama}`"
                                    @change="statuses[siswa.id] = status"
                                />
                            </td>
                        </tr>
                        <tr v-if="props.siswa.length === 0">
                            <td colspan="6" class="px-5 py-12 text-center text-muted-foreground">
                                Pilih kelas untuk menampilkan daftar siswa.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" variant="ghost" @click="routerBack">Batal</Button>
                <Button type="submit" :disabled="form.processing || props.siswa.length === 0">Simpan</Button>
            </div>
        </form>
    </div>
</template>
