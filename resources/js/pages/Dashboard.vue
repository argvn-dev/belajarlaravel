<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { GraduationCap, School, Users } from '@lucide/vue';
import { dashboard } from '@/routes';

const studentsByClass = [
    { name: 'X IPA 1', total: 32 },
    { name: 'X IPA 2', total: 28 },
    { name: 'X IPA 3', total: 31 },
    { name: 'X IPS 1', total: 29 },
    { name: 'X IPS 2', total: 33 },
    { name: 'XI IPA 1', total: 36 },
    { name: 'XI IPA 2', total: 30 },
    { name: 'XI IPS 1', total: 30 },
    { name: 'XI IPS 2', total: 27 },
    { name: 'XII IPA 1', total: 34 },
    { name: 'XII IPA 2', total: 32 },
    { name: 'XII IPS 1', total: 28 },
];

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <div class="grid gap-4 md:grid-cols-3">
            <div
                class="rounded-2xl border border-sidebar-border/70 bg-background p-5 shadow-sm dark:border-sidebar-border"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Jumlah Siswa
                        </p>
                        <p class="mt-3 text-3xl font-bold tracking-tight">0</p>
                    </div>
                    <div
                        class="rounded-xl bg-blue-500/10 p-3 text-blue-600 dark:text-blue-400"
                    >
                        <Users class="size-6" aria-hidden="true" />
                    </div>
                </div>
            </div>
            <div
                class="rounded-2xl border border-sidebar-border/70 bg-background p-5 shadow-sm dark:border-sidebar-border"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Jumlah Guru
                        </p>
                        <p class="mt-3 text-3xl font-bold tracking-tight">0</p>
                    </div>
                    <div
                        class="rounded-xl bg-amber-500/10 p-3 text-amber-600 dark:text-amber-400"
                    >
                        <GraduationCap class="size-6" aria-hidden="true" />
                    </div>
                </div>
            </div>
            <div
                class="rounded-2xl border border-sidebar-border/70 bg-background p-5 shadow-sm dark:border-sidebar-border"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Jumlah Kelas
                        </p>
                        <p class="mt-3 text-3xl font-bold tracking-tight">0</p>
                    </div>
                    <div
                        class="rounded-xl bg-emerald-500/10 p-3 text-emerald-600 dark:text-emerald-400"
                    >
                        <School class="size-6" aria-hidden="true" />
                    </div>
                </div>
            </div>
        </div>
        <div
            class="min-h-80 flex-1 rounded-2xl border border-sidebar-border/70 bg-background p-6 shadow-sm dark:border-sidebar-border"
        >
            <h2 class="text-lg font-semibold">Ringkasan Dashboard</h2>
            <p class="mt-1 text-sm text-muted-foreground">
                Jumlah siswa per kelas
            </p>
            <div class="mt-8 overflow-x-auto pb-2">
                <div
                    class="grid h-56 min-w-225 grid-cols-12 items-end gap-4 border-b border-sidebar-border/70 px-2 dark:border-sidebar-border"
                >
                    <div
                        v-for="classroom in studentsByClass"
                        :key="classroom.name"
                        class="flex h-full flex-col justify-end text-center"
                    >
                        <span class="mb-2 text-sm font-semibold">{{
                            classroom.total
                        }}</span>
                        <div
                            class="rounded-t-lg bg-blue-500 transition-colors hover:bg-blue-600"
                            :style="{
                                height: `${(classroom.total / 40) * 100}%`,
                            }"
                            :aria-label="`${classroom.name}: ${classroom.total} siswa`"
                        />
                        <span class="mt-3 text-xs text-muted-foreground">
                            {{ classroom.name }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
