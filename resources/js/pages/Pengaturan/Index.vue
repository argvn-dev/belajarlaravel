<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CalendarClock, School, CalendarDays } from '@lucide/vue';
import { dashboard, pengaturan } from '@/routes';
import kelas from '@/routes/kelas';
import tahunAjaran from '@/routes/tahun-ajaran';
import jadwalPelajaran from '@/routes/jadwal-pelajaran';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Pengaturan', href: pengaturan() },
        ],
    },
});

const menus = [
    {
        title: 'Tahun Ajaran',
        description: 'Kelola daftar tahun ajaran.',
        icon: CalendarDays,
        href: tahunAjaran.index().url,
    },
    {
        title: 'Kelas',
        description: 'Kelola data kelas sekolah.',
        icon: School,
        href: kelas.index().url,
    },
    {
        title: 'Jadwal Pelajaran',
        description: 'Kelola jadwal mengajar setiap kelas.',
        icon: CalendarClock,
        href: jadwalPelajaran.index().url,
    },
];
</script>

<template>
    <Head title="Pengaturan" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="grid gap-4">
            <Link
                v-for="menu in menus"
                :key="menu.href"
                :href="menu.href"
                class="flex items-center gap-4 rounded-xl border border-sidebar-border/70 bg-background p-4 shadow-sm transition-colors hover:bg-muted/50 dark:border-sidebar-border"
            >
                <div
                    class="rounded-xl bg-blue-500/10 p-3 text-blue-600 dark:text-blue-400"
                >
                    <component :is="menu.icon" class="size-6" />
                </div>
                <div class="min-w-0">
                    <p class="font-medium">{{ menu.title }}</p>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        {{ menu.description }}
                    </p>
                </div>
            </Link>
        </div>
    </div>
</template>
