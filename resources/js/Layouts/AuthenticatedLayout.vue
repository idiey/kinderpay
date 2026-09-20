<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  HomeIcon, UsersIcon, AcademicCapIcon, CurrencyDollarIcon, DocumentTextIcon,
  CreditCardIcon, UserGroupIcon, CalendarIcon, BanknotesIcon, ChartBarIcon,
  CogIcon, Bars3Icon, XMarkIcon
} from '@heroicons/vue/24/outline';

const page = usePage();
const user = computed(() => page.props.auth.user);

const hasRole = (roleOrRoles) => {
  const userRoles = page.props.auth.roles || (user.value?.roles ? user.value.roles.map(r => r.name) : []);
  if (!userRoles.length) return false;
  if (Array.isArray(roleOrRoles)) {
    return roleOrRoles.some(r => userRoles.includes(r));
  }
  return userRoles.includes(roleOrRoles);
};

const navigation = computed(() => [
  { name: 'Dashboard', href: route('dashboard'), icon: HomeIcon, show: true, active: route().current('dashboard') },
  { name: 'Students', href: route('students.index'), icon: UsersIcon, show: hasRole(['admin', 'accounts', 'teacher']), active: route().current('students.*') },
  { name: 'Classes', href: route('classes.index'), icon: AcademicCapIcon, show: hasRole(['admin', 'teacher']), active: route().current('classes.*') },
  { name: 'Fee Templates', href: route('fee-templates.index'), icon: CurrencyDollarIcon, show: hasRole(['admin', 'accounts']), active: route().current('fee-templates.*') },
  { name: 'Invoices', href: route('invoices.index'), icon: DocumentTextIcon, show: hasRole(['admin', 'accounts']), active: route().current('invoices.*') },
  { name: 'Payments', href: route('payments.index'), icon: CreditCardIcon, show: hasRole(['admin', 'accounts']), active: route().current('payments.*') },
  { name: 'Staff', href: route('staff.index'), icon: UserGroupIcon, show: hasRole(['admin']), active: route().current('staff.*') },
  { name: 'Leave', href: route('leave.index'), icon: CalendarIcon, show: hasRole(['admin', 'teacher']), active: route().current('leave.*') },
  { name: 'Payroll', href: route('payroll.index'), icon: BanknotesIcon, show: hasRole(['admin', 'accounts']), active: route().current('payroll.*') },
  { name: 'Finance Reports', href: route('reports.finance'), icon: ChartBarIcon, show: hasRole(['admin', 'accounts']), active: route().current('reports.finance') },
  { name: 'Settings', href: route('settings.index'), icon: CogIcon, show: hasRole(['admin']), active: route().current('settings.*') },
]);

const sidebarOpen = ref(false);
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Mobile sidebar -->
    <div v-show="sidebarOpen" class="fixed inset-0 z-40 flex md:hidden" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-gray-600 bg-opacity-75" aria-hidden="true" @click="sidebarOpen = false"></div>
      <div class="relative flex w-full max-w-xs flex-1 flex-col bg-gray-800 pt-5 pb-4">
        <div class="absolute top-0 right-0 -mr-12 pt-2">
          <button type="button" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebarOpen = false">
            <span class="sr-only">Close sidebar</span>
            <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
          </button>
        </div>
        <div class="flex flex-shrink-0 items-center px-4">
          <span class="text-white text-2xl font-bold">KinderPay</span>
        </div>
        <div class="mt-5 h-0 flex-1 overflow-y-auto">
          <nav class="space-y-1 px-2">
            <template v-for="item in navigation" :key="item.name">
              <Link v-if="item.show" :href="item.href" :class="[item.active ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'group flex items-center px-2 py-2 text-base font-medium rounded-md']">
                <component :is="item.icon" :class="[item.active ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300', 'mr-4 flex-shrink-0 h-6 w-6']" aria-hidden="true" />
                {{ item.name }}
              </Link>
            </template>
          </nav>
        </div>
      </div>
      <div class="w-14 flex-shrink-0" aria-hidden="true"></div>
    </div>

    <!-- Desktop sidebar -->
    <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
      <div class="flex flex-col flex-grow pt-5 bg-gray-800 overflow-y-auto">
        <div class="flex items-center flex-shrink-0 px-4">
          <span class="text-white text-2xl font-bold">KinderPay</span>
        </div>
        <div class="mt-5 flex-1 flex flex-col">
          <nav class="flex-1 px-2 pb-4 space-y-1">
            <template v-for="item in navigation" :key="item.name">
              <Link v-if="item.show" :href="item.href" :class="[item.active ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'group flex items-center px-2 py-2 text-sm font-medium rounded-md']">
                <component :is="item.icon" :class="[item.active ? 'text-indigo-200' : 'text-gray-400 group-hover:text-gray-300', 'mr-3 flex-shrink-0 h-6 w-6']" aria-hidden="true" />
                {{ item.name }}
              </Link>
            </template>
          </nav>
        </div>
      </div>
    </div>

    <div class="md:pl-64 flex flex-col flex-1">
      <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow border-b border-gray-200">
        <button type="button" class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden" @click="sidebarOpen = true">
          <span class="sr-only">Open sidebar</span>
          <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>
        <div class="flex flex-1 justify-between px-4">
          <div class="flex flex-1"></div>
          <div class="ml-4 flex items-center md:ml-6 group relative cursor-pointer">
            <div class="flex items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 py-2">
              <span class="text-gray-700 font-medium">{{ user?.name }}</span>
            </div>
            <div class="absolute right-0 top-full z-50 mt-1 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 hidden group-hover:block border border-gray-200">
              <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</Link>
              <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</Link>
            </div>
          </div>
        </div>
      </div>

      <main class="flex-1">
        <div class="py-6">
          <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
            <slot />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
