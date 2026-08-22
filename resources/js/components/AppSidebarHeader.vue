<script setup lang="ts">
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import {
    Avatar,
    AvatarFallback,
    AvatarImage,
} from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const auth = computed(() => page.props.auth);
const isDashboard = computed(
    () => page.url.split('?')[0] === dashboard().url,
);
</script>

<template>
    <header
        class="fixed inset-x-0 top-0 z-40 flex h-16 items-center gap-2 border-b border-sidebar-border/70 bg-background px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:static md:bg-transparent md:px-4"
    >
        <div class="flex items-center gap-2">
            <button
                v-if="!isDashboard"
                type="button"
                class="-ml-1 flex size-9 items-center justify-center rounded-md hover:bg-accent md:hidden"
                :aria-label="'Kembali'"
                @click="router.back()"
            >
                <ArrowLeft class="h-5 w-5" />
            </button>

            <SidebarTrigger class="-ml-1 hidden md:inline-flex" />

            <Breadcrumbs
                v-if="breadcrumbs && breadcrumbs.length > 0"
                :breadcrumbs="breadcrumbs"
                class="hidden md:block"
            />
            <span
                v-if="breadcrumbs && breadcrumbs.length > 0"
                class="font-semibold text-foreground md:hidden"
                >{{ breadcrumbs[breadcrumbs.length - 1]?.title }}</span
            >
        </div>

        <div class="ml-auto md:hidden">
            <DropdownMenu>
            <DropdownMenuTrigger :as-child="true">
                <Button
                    variant="ghost"
                    size="icon"
                    class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                >
                    <Avatar class="size-8 overflow-hidden rounded-full">
                        <AvatarImage
                            v-if="auth.user.avatar"
                            :src="auth.user.avatar"
                            :alt="auth.user.name"
                        />
                        <AvatarFallback
                            class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
                            >{{ getInitials(auth.user?.name) }}</AvatarFallback
                        >
                    </Avatar>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-56">
                <UserMenuContent :user="auth.user" />
            </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
