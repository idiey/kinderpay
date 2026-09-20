<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { PlusIcon, TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  templates: Array,
});

const isCreating = ref(false);
const editingTemplate = ref(null);

const form = useForm({
  name: '',
  type: 'recurring',
  amount: '',
  frequency: 'monthly',
  description: '',
  is_active: true,
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const openCreate = () => {
  editingTemplate.value = null;
  form.reset();
  form.type = 'recurring';
  form.frequency = 'monthly';
  form.is_active = true;
  isCreating.value = true;
};

const openEdit = (tpl) => {
  editingTemplate.value = tpl;
  form.name = tpl.name;
  form.type = tpl.type;
  form.amount = tpl.amount;
  form.frequency = tpl.frequency;
  form.description = tpl.description || '';
  form.is_active = Boolean(tpl.is_active);
  isCreating.value = true;
};

const submit = () => {
  if (editingTemplate.value) {
    form.put(route('fee-templates.update', editingTemplate.value.id), {
      onSuccess: () => {
        isCreating.value = false;
        editingTemplate.value = null;
      }
    });
  } else {
    form.post(route('fee-templates.store'), {
      onSuccess: () => {
        isCreating.value = false;
        form.reset();
      }
    });
  }
};

const deleteTemplate = (id) => {
  if (confirm('Are you sure? If assigned to students, it will be deactivated.')) {
    useForm({}).delete(route('fee-templates.destroy', id));
  }
};
</script>

<template>
  <Head title="Fee Templates" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Fee Templates</h1>
          <p class="text-sm text-gray-500 mt-1">Configure standard tuition rates and recurring/one-time billing items</p>
        </div>
        <button
          @click="openCreate"
          class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <PlusIcon class="w-4 h-4 mr-1.5" />
          Create Fee Template
        </button>
      </div>

      <!-- Create / Edit Form Modal or Card -->
      <div v-if="isCreating" class="bg-white p-6 rounded-lg shadow border border-indigo-100">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ editingTemplate ? 'Edit Fee Template' : 'Create New Fee Template' }}
        </h3>
        <form @submit.prevent="submit" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 uppercase">Fee Name *</label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="e.g. Monthly Tuition / Yuran Bulanan"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Amount (RM) *</label>
            <input
              v-model="form.amount"
              type="number"
              step="0.01"
              required
              placeholder="350.00"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Fee Type *</label>
            <select
              v-model="form.type"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="recurring">Recurring</option>
              <option value="one_time">One-Time</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Billing Frequency *</label>
            <select
              v-model="form.frequency"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="monthly">Monthly</option>
              <option value="quarterly">Quarterly</option>
              <option value="yearly">Yearly</option>
              <option value="once">Once</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Status</label>
            <select
              v-model="form.is_active"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option :value="true">Active</option>
              <option :value="false">Inactive</option>
            </select>
          </div>

          <div class="sm:col-span-3">
            <label class="block text-xs font-semibold text-gray-700 uppercase">Description</label>
            <input
              v-model="form.description"
              type="text"
              placeholder="Optional remarks about this fee category"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <div class="sm:col-span-3 flex justify-end space-x-3 mt-2">
            <button
              type="button"
              @click="isCreating = false"
              class="px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md"
            >
              Save Template
            </button>
          </div>
        </form>
      </div>

      <!-- Templates Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Template Name</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type & Frequency</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Standard Rate</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Assigned Students</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white">
            <tr v-for="t in templates" :key="t.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4">
                <div class="font-bold text-gray-900">{{ t.name }}</div>
                <div v-if="t.description" class="text-xs text-gray-400">{{ t.description }}</div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600 capitalize">
                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                  {{ t.type }} &bull; {{ t.frequency }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                {{ formatCurrency(t.amount) }}
              </td>
              <td class="px-6 py-4 text-center text-sm text-gray-600 font-medium">
                {{ t.student_fees_count || 0 }}
              </td>
              <td class="px-6 py-4 text-center">
                <span
                  class="px-2 py-0.5 text-xs font-semibold rounded-full"
                  :class="t.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                >
                  {{ t.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                <button
                  @click="openEdit(t)"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  Edit
                </button>
                <button
                  @click="deleteTemplate(t.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="templates.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                No fee templates configured yet. Click "Create Fee Template" to begin.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
