<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { MagnifyingGlassIcon, PlusIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  students: Object,
  classes: Array,
  filters: Object
});

const search = ref(props.filters.search || '');
const classId = ref(props.filters.class_id || '');
const status = ref(props.filters.status || '');

const applyFilters = () => {
  router.get(route('students.index'), {
    search: search.value || undefined,
    class_id: classId.value || undefined,
    status: status.value || undefined,
  }, {
    preserveState: true,
    replace: true,
  });
};

watch([classId, status], () => {
  applyFilters();
});

let debounceTimer = null;
const handleSearchInput = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    applyFilters();
  }, 400);
};

const getStatusBadge = (st) => {
  switch (st) {
    case 'active':
      return 'bg-green-100 text-green-800';
    case 'graduated':
      return 'bg-blue-100 text-blue-800';
    case 'withdrawn':
      return 'bg-gray-100 text-gray-700';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};
</script>

<template>
  <Head title="Students Directory" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Students</h1>
          <p class="text-sm text-gray-500 mt-1">Manage enrolled children, class allocations and guardian contacts</p>
        </div>
        <Link
          :href="route('students.create')"
          class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <PlusIcon class="w-4 h-4 mr-1.5" />
          Enroll New Student
        </Link>
      </div>

      <!-- Filters Bar -->
      <div class="bg-white p-4 rounded-lg shadow border border-gray-100 flex flex-col sm:flex-row gap-3 items-center">
        <div class="relative flex-1 w-full">
          <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" />
          <input
            v-model="search"
            @input="handleSearchInput"
            type="text"
            placeholder="Search by student name..."
            class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        <div class="w-full sm:w-48">
          <select
            v-model="classId"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">All Classes</option>
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div class="w-full sm:w-40">
          <select
            v-model="status"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="graduated">Graduated</option>
            <option value="withdrawn">Withdrawn</option>
          </select>
        </div>
      </div>

      <!-- Students Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Student Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Class</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Primary Guardian</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="s in students.data" :key="s.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-gray-900">{{ s.name }}</div>
                  <div class="text-xs text-gray-400">Gender: {{ s.gender }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <span v-if="s.class_group" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                    {{ s.class_group.name }}
                  </span>
                  <span v-else class="text-xs text-gray-400 italic">Unassigned</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                  {{ s.guardians?.[0]?.name || '-' }}
                  <span v-if="s.guardians?.[0]" class="text-xs text-gray-400 block">({{ s.guardians[0].relationship }})</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ s.guardians?.[0]?.phone || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span :class="['px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full uppercase tracking-wider', getStatusBadge(s.status)]">
                    {{ s.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link :href="route('students.show', s.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">View</Link>
                  <Link :href="route('students.edit', s.id)" class="text-gray-500 hover:text-gray-700">Edit</Link>
                </td>
              </tr>
              <tr v-if="students.data.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                  No students found matching current filters.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="students.links && students.links.length > 3" class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
          <div class="text-xs text-gray-500">
            Showing {{ students.from || 0 }} to {{ students.to || 0 }} of {{ students.total }} students
          </div>
          <div class="flex space-x-1">
            <template v-for="(link, i) in students.links" :key="i">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100', 'px-3 py-1 text-xs rounded border border-gray-300']"
                v-html="link.label"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
