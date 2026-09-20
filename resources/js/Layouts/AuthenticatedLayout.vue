<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  HomeIcon, UsersIcon, AcademicCapIcon, CurrencyDollarIcon, DocumentTextIcon,
  CreditCardIcon, UserGroupIcon, CalendarIcon, BanknotesIcon, ChartBarIcon,
  CogIcon, Bars3Icon, XMarkIcon, BuildingLibraryIcon, ChevronDownIcon, CheckIcon
} from '@heroicons/vue/24/outline';
import { router } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentKindergarten = computed(() => page.props.auth.kindergarten);
const availableKindergartens = computed(() => page.props.auth.available_kindergartens || []);

const hasRole = (roleOrRoles) => {
  const userRoles = page.props.auth.roles || (user.value?.roles ? user.value.roles.map(r => r.name) : []);
  if (!userRoles.length) return false;
  if (Array.isArray(roleOrRoles)) {
    return roleOrRoles.some(r => userRoles.includes(r));
  }
  return userRoles.includes(roleOrRoles);
};

const isSuperAdmin = computed(() => hasRole('super_admin') || user.value?.role === 'super_admin');

const switchBranch = (kindergartenId) => {
  if (currentKindergarten.value?.id === kindergartenId) return;
  router.post(route('tenants.switch'), { kindergarten_id: kindergartenId }, {
    preserveScroll: true,
    preserveState: false,
  });
};

const navigation = computed(() => [
  { name: 'Dashboard', href: route('dashboard'), icon: HomeIcon, show: true, active: route().current('dashboard') },
  { name: 'Kindergarten Branches', href: route('kindergartens.index'), icon: BuildingLibraryIcon, show: isSuperAdmin.value, active: route().current('kindergartens.*') },
  { name: 'Students', href: route('students.index'), icon: UsersIcon, show: hasRole(['admin', 'accounts', 'teacher']) || isSuperAdmin.value, active: route().current('students.*') },
  { name: 'Classes', href: route('classes.index'), icon: AcademicCapIcon, show: hasRole(['admin', 'teacher']) || isSuperAdmin.value, active: route().current('classes.*') },
  { name: 'Fee Templates', href: route('fee-templates.index'), icon: CurrencyDollarIcon, show: hasRole(['admin', 'accounts']) || isSuperAdmin.value, active: route().current('fee-templates.*') },
  { name: 'Invoices', href: route('invoices.index'), icon: DocumentTextIcon, show: hasRole(['admin', 'accounts']) || isSuperAdmin.value, active: route().current('invoices.*') },
  { name: 'Payments', href: route('payments.index'), icon: CreditCardIcon, show: hasRole(['admin', 'accounts']) || isSuperAdmin.value, active: route().current('payments.*') },
  { name: 'Staff', href: route('staff.index'), icon: UserGroupIcon, show: hasRole(['admin']) || isSuperAdmin.value, active: route().current('staff.*') },
  { name: 'Leave', href: route('leave.index'), icon: CalendarIcon, show: hasRole(['admin', 'teacher']) || isSuperAdmin.value, active: route().current('leave.*') },
  { name: 'Payroll', href: route('payroll.index'), icon: BanknotesIcon, show: hasRole(['admin', 'accounts']) || isSuperAdmin.value, active: route().current('payroll.*') },
  { name: 'Finance Reports', href: route('reports.finance'), icon: ChartBarIcon, show: hasRole(['admin', 'accounts']) || isSuperAdmin.value, active: route().current('reports.finance') },
  { name: 'Settings', href: route('settings.index'), icon: CogIcon, show: hasRole(['admin']) || isSuperAdmin.value, active: route().current('settings.*') },
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
          <!-- Active Branch Indicator / Switcher -->
          <div class="flex items-center">
            <div v-if="availableKindergartens.length > 1 && isSuperAdmin" class="relative group cursor-pointer">
              <button type="button" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50/70 hover:bg-indigo-100 text-indigo-900 text-xs font-semibold shadow-sm transition">
                <BuildingLibraryIcon class="w-4 h-4 text-indigo-600" />
                <span class="max-w-[140px] sm:max-w-xs truncate">{{ currentKindergarten?.name || 'Select Branch' }}</span>
                <span class="bg-indigo-200 text-indigo-800 text-[10px] px-1.5 py-0.2 rounded font-mono">{{ currentKindergarten?.invoice_prefix }}</span>
                <ChevronDownIcon class="w-3.5 h-3.5 text-indigo-500 ml-0.5" />
              </button>

              <div class="absolute left-0 top-full z-50 mt-1 w-64 origin-top-left rounded-xl bg-white p-1.5 shadow-xl ring-1 ring-black ring-opacity-5 hidden group-hover:block border border-gray-100">
                <div class="px-2 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 flex items-center justify-between">
                  <span>Switch Branch</span>
                  <span>{{ availableKindergartens.length }} Total</span>
                </div>
                <div class="max-h-56 overflow-y-auto py-1">
                  <button
                    v-for="k in availableKindergartens"
                    :key="k.id"
                    type="button"
                    @click="switchBranch(k.id)"
                    class="w-full text-left px-2.5 py-2 rounded-lg text-xs flex items-center justify-between hover:bg-indigo-50 transition"
                    :class="currentKindergarten?.id === k.id ? 'bg-indigo-50/80 font-bold text-indigo-700' : 'text-gray-700'"
                  >
                    <div class="truncate">
                      <div class="truncate">{{ k.name }}</div>
                      <div class="text-[10px] text-gray-400">{{ k.city }}, {{ k.state }}</div>
                    </div>
                    <CheckIcon v-if="currentKindergarten?.id === k.id" class="w-4 h-4 text-indigo-600 flex-shrink-0" />
                  </button>
                </div>
                <div class="border-t border-gray-100 pt-1 mt-1">
                  <Link :href="route('kindergartens.index')" class="w-full text-left block px-2.5 py-1.5 text-xs text-indigo-600 font-semibold hover:bg-indigo-50 rounded-lg">
                    + Manage All Branches
                  </Link>
                </div>
              </div>
            </div>

            <!-- Single Branch Info for Normal Staff -->
            <div v-else-if="currentKindergarten" class="flex items-center gap-1.5 px-3 py-1 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-700">
              <BuildingLibraryIcon class="w-4 h-4 text-gray-500" />
              <span class="font-medium truncate max-w-xs">{{ currentKindergarten.name }}</span>
            </div>
          </div>

          <div class="ml-4 flex items-center md:ml-6 gap-2">
            <!-- Dedicated Topbar Logout Button -->
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-gray-600 text-xs font-semibold transition"
              title="Log Out"
            >
              <span>Logout</span>
            </Link>

            <!-- User Menu Dropdown -->
            <Dropdown align="right" width="48">
              <template #trigger>
                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-xs font-medium text-gray-700 transition focus:outline-none">
                  <span>{{ user?.name }}</span>
                  <span v-if="isSuperAdmin" class="text-[9px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded font-bold">SUPER ADMIN</span>
                  <ChevronDownIcon class="w-3.5 h-3.5 text-gray-400" />
                </button>
              </template>

              <template #content>
                <div class="px-4 py-2 border-b border-gray-100">
                  <p class="text-xs text-gray-500">Signed in as</p>
                  <p class="text-xs font-semibold text-gray-900 truncate">{{ user?.name }}</p>
                  <p class="text-[10px] text-gray-400 truncate">{{ user?.email }}</p>
                </div>
                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                <DropdownLink v-if="isSuperAdmin" :href="route('kindergartens.index')" class="text-indigo-600 font-semibold">
                  Kindergarten Branches
                </DropdownLink>
                <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-600 hover:bg-red-50 font-semibold">
                  Log Out
                </DropdownLink>
              </template>
            </Dropdown>
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
