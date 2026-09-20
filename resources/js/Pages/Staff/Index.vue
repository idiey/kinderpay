<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { MagnifyingGlassIcon, PlusIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  staff: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const applyFilters = () => {
  router.get(route('staff.index'), {
    search: search.value || undefined,
    status: status.value || undefined,
  }, {
    preserveState: true,
    replace: true,
  });
};

watch(status, () => applyFilters());

let debounce = null;
const handleSearch = () => {
  clearTimeout(debounce);
  debounce = setTimeout(() => applyFilters(), 400);
};

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};
</script>

<template>
  <Head title="Staff Directory" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Staff & Teachers</h1>
          <p class="text-sm text-gray-500 mt-1">Manage kindergarten educators, staff salaries, allowances, and statutory settings</p>
        </div>
        <Link
          :href="route('staff.create')"
          class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <PlusIcon class="w-4 h-4 mr-1.5" />
          Add New Staff
        </Link>
      </div>

      <!-- Filters -->
      <div class="bg-white p-4 rounded-lg shadow border border-gray-100 flex flex-col sm:flex-row gap-3 items-center">
        <div class="relative flex-1 w-full">
          <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" />
          <input
            v-model="search"
            @input="handleSearch"
            type="text"
            placeholder="Search staff name, designation, or employee code..."
            class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        <div class="w-full sm:w-40">
          <select
            v-model="status"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="resigned">Resigned</option>
            <option value="terminated">Terminated</option>
          </select>
        </div>
      </div>

      <!-- Staff Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Staff Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Designation</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Basic Salary</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="s in staff.data" :key="s.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-bold text-gray-900">{{ s.name }}</div>
                  <div class="text-xs text-gray-400">ID: {{ s.employee_id || 'N/A' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                    {{ s.position }}
                  </span>
                  <span class="text-xs text-gray-400 block capitalize mt-0.5">{{ s.employment_type?.replace('_', ' ') }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  <div>{{ s.phone }}</div>
                  <div class="text-xs text-gray-400">{{ s.email || '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                  {{ formatCurrency(s.basic_salary) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span
                    class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase"
                    :class="s.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                  >
                    {{ s.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <Link :href="route('staff.show', s.id)" class="text-indigo-600 hover:text-indigo-900">View</Link>
                  <Link :href="route('staff.edit', s.id)" class="text-gray-500 hover:text-gray-700">Edit</Link>
                </td>
              </tr>
              <tr v-if="staff.data.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                  No staff members registered. Click "Add New Staff" to create an employee record.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
