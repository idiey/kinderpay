<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

const navigation = [
  { name: 'Dashboard', href: route('dashboard'), active: route().current('dashboard') },
  { name: 'Leave', href: route('leave.index'), active: route().current('leave.*') },
  { name: 'Payslips', href: route('payslips.index'), active: route().current('payslips.*') },
  { name: 'Profile', href: route('profile.edit'), active: route().current('profile.*') },
];
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-blue-600 shadow">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
          <div class="flex">
            <div class="flex flex-shrink-0 items-center">
              <span class="text-white text-2xl font-bold">KinderPay</span>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <Link v-for="item in navigation" :key="item.name" :href="item.href"
                :class="[item.active ? 'border-white text-white' : 'border-transparent text-blue-100 hover:border-blue-200 hover:text-white', 'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium']">
                {{ item.name }}
              </Link>
            </div>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:items-center group relative cursor-pointer">
             <div class="flex items-center rounded-full text-sm focus:outline-none py-2">
                <span class="text-white font-medium px-2 py-1">{{ user?.name }}</span>
             </div>
             <div class="absolute right-0 top-full z-50 mt-1 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 hidden group-hover:block border border-gray-200">
                <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</Link>
                <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</Link>
             </div>
          </div>
        </div>
      </div>
    </nav>
    <main class="py-10">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>
