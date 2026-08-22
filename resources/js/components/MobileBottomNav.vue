<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    CalendarCheck,
    GraduationCap,
    Home,
    Settings,
    Users,
} from '@lucide/vue';
import { dashboard, pengaturan } from '@/routes';
import guru from '@/routes/guru';
import siswa from '@/routes/siswa';
import kehadiran from '@/routes/kehadiran';

const page = usePage();
const currentPath = computed(() => page.url.split('?')[0]);

const items = [
    { label: 'Home', icon: Home, href: dashboard().url },
    { label: 'Siswa', icon: GraduationCap, href: siswa.index().url },
    { label: 'Kehadiran', icon: CalendarCheck, href: kehadiran.index().url },
    { label: 'Guru', icon: Users, href: guru.index().url },
    { label: 'Pengaturan', icon: Settings, href: pengaturan().url },
];

const isActive = (href: string) => currentPath.value === href;
</script>

<template>
    <nav
        class="fixed inset-x-0 bottom-0 z-50 border-t border-sidebar-border/70 bg-background/95 backdrop-blur md:hidden dark:border-sidebar-border"
    >
        <ul class="flex items-stretch justify-around">
            <li v-for="item in items" :key="item.href">
                <Link
                    :href="item.href"
                    class="flex flex-col items-center gap-1 px-2 py-2.5 text-[10px] font-medium transition-colors"
                    :class="
                        isActive(item.href)
                            ? 'text-foreground'
                            : 'text-muted-foreground'
                    "
                >
                    <component
                        :is="item.icon"
                        class="size-5"
                        :class="
                            isActive(item.href)
                                ? 'text-blue-600 dark:text-blue-400'
                                : ''
                        "
                    />
                    {{ item.label }}
                </Link>
            </li>
        </ul>
    </nav>
</template>
