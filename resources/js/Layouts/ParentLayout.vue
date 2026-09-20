<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import {
  Bars3Icon,
  XMarkIcon,
  ArrowRightOnRectangleIcon,
  UserCircleIcon,
  ChevronDownIcon
} from '@heroicons/vue/24/outline';

const page = usePage();
const user = computed(() => page.props.auth.user);
const mobileMenuOpen = ref(false);

const navigation = [
  { name: 'Dashboard', href: route('parent.dashboard'), active: route().current('parent.dashboard') },
  { name: 'My Invoices', href: route('parent.invoices'), active: route().current('parent.invoices*') },
  { name: 'Payment History', href: route('parent.history'), active: route().current('parent.history') },
  { name: 'Profile', href: route('profile.edit'), active: route().current('profile.*') },
];
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-emerald-600 shadow sticky top-0 z-50">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between items-center">
          <div class="flex items-center">
            <Link :href="route('parent.dashboard')" class="flex flex-shrink-0 items-center gap-2">
              <span class="text-white text-2xl font-bold tracking-tight">KinderPay</span>
              <span class="bg-emerald-800/60 text-emerald-100 text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full border border-emerald-400/30">Parent</span>
            </Link>
            <div class="hidden md:ml-8 md:flex md:space-x-4">
              <Link v-for="item in navigation" :key="item.name" :href="item.href"
                :class="[item.active ? 'bg-emerald-700 text-white shadow-inner' : 'text-emerald-100 hover:bg-emerald-500/80 hover:text-white', 'px-3 py-2 rounded-lg text-sm font-medium transition']">
                {{ item.name }}
              </Link>
            </div>
          </div>

          <!-- Desktop User Menu & Logout -->
          <div class="hidden md:flex md:items-center md:gap-3">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button type="button" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-700/60 hover:bg-emerald-700 text-white text-sm font-medium transition border border-emerald-500/50 focus:outline-none">
                  <UserCircleIcon class="w-5 h-5 text-emerald-200" />
                  <span class="max-w-[150px] truncate">{{ user?.name }}</span>
                  <ChevronDownIcon class="w-4 h-4 text-emerald-200" />
                </button>
              </template>

              <template #content>
                <div class="px-4 py-2 border-b border-gray-100">
                  <p class="text-xs text-gray-500">Signed in as</p>
                  <p class="text-sm font-semibold text-gray-900 truncate">{{ user?.name }}</p>
                  <p class="text-xs text-gray-400 truncate">{{ user?.email }}</p>
                </div>
                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-600 hover:bg-red-50">
                  Log Out
                </DropdownLink>
              </template>
            </Dropdown>

            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-red-600 text-white text-xs font-semibold shadow-sm transition"
              title="Log Out"
            >
              <ArrowRightOnRectangleIcon class="w-4 h-4" />
              <span>Logout</span>
            </Link>
          </div>

          <!-- Mobile Hamburger Toggle -->
          <div class="flex items-center md:hidden gap-2">
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-red-600 text-white text-xs font-semibold"
            >
              <ArrowRightOnRectangleIcon class="w-4 h-4" />
              <span>Logout</span>
            </Link>
            <button
              type="button"
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="inline-flex items-center justify-center p-2 rounded-md text-emerald-100 hover:bg-emerald-700 hover:text-white focus:outline-none"
            >
              <span class="sr-only">Open main menu</span>
              <Bars3Icon v-if="!mobileMenuOpen" class="block h-6 w-6" />
              <XMarkIcon v-else class="block h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu Dropdown -->
      <div v-show="mobileMenuOpen" class="md:hidden border-t border-emerald-500 bg-emerald-700/95 px-4 pt-2 pb-4 space-y-1">
        <div class="pb-2 mb-2 border-b border-emerald-600 text-emerald-100 text-xs">
          Signed in as: <strong class="text-white">{{ user?.name }}</strong>
        </div>
        <Link
          v-for="item in navigation"
          :key="item.name"
          :href="item.href"
          @click="mobileMenuOpen = false"
          :class="[item.active ? 'bg-emerald-800 text-white font-bold' : 'text-emerald-100 hover:bg-emerald-600 hover:text-white', 'block px-3 py-2 rounded-md text-base font-medium']"
        >
          {{ item.name }}
        </Link>
        <Link
          :href="route('logout')"
          method="post"
          as="button"
          class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-md text-base font-medium text-red-200 hover:bg-red-600 hover:text-white mt-2"
        >
          <ArrowRightOnRectangleIcon class="w-5 h-5" />
          <span>Log Out</span>
        </Link>
      </div>
    </nav>
    <main class="py-10">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>
