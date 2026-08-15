<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    CalendarCheck,
    CalendarDays,
    FolderGit2,
    LayoutGrid,
    School,
    UserRound,
    UsersRound,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import guru from '@/routes/guru';
import jadwalPelajaran from '@/routes/jadwal-pelajaran';
import kehadiran from '@/routes/kehadiran';
import kelas from '@/routes/kelas';
import siswa from '@/routes/siswa';
import tahunAjaran from '@/routes/tahun-ajaran';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Tahun Ajaran',
        href: tahunAjaran.index(),
        icon: CalendarDays,
    },
    {
        title: 'Kelas',
        href: kelas.index(),
        icon: School,
    },
    {
        title: 'Guru',
        href: guru.index(),
        icon: UsersRound,
    },
    {
        title: 'Siswa',
        href: siswa.index(),
        icon: UserRound,
    },
    {
        title: 'Jadwal Pelajaran',
        href: jadwalPelajaran.index(),
        icon: CalendarDays,
    },
    {
        title: 'Kehadiran',
        href: kehadiran.index(),
        icon: CalendarCheck,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
