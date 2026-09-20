<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { PlusIcon, BanknotesIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  runs: Object,
});

const isModalOpen = ref(false);
const form = useForm({
  month: new Date().getMonth() + 1,
  year: new Date().getFullYear(),
});

const submit = () => {
  form.post(route('payroll.generate'), {
    onSuccess: () => {
      isModalOpen.value = false;
    }
  });
};

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const monthName = (m) => {
  const date = new Date(2026, m - 1, 1);
  return date.toLocaleString('default', { month: 'long' });
};
</script>

<template>
  <Head title="Staff Payroll" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Monthly Payroll</h1>
          <p class="text-sm text-gray-500 mt-1">Automated Malaysian payroll engine with EPF, SOCSO, EIS & unpaid leave deductions</p>
        </div>
        <button
          @click="isModalOpen = true"
          class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <BanknotesIcon class="w-4 h-4 mr-1.5" />
          Run Monthly Payroll
        </button>
      </div>

      <!-- Payroll Runs Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="run in runs.data"
          :key="run.id"
          class="bg-white rounded-lg shadow border border-gray-100 p-6 flex flex-col justify-between hover:border-indigo-200 transition"
        >
          <div>
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold text-gray-900">{{ monthName(run.month) }} {{ run.year }}</span>
              <span
                class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase"
                :class="run.status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
              >
                {{ run.status }}
              </span>
            </div>
            <div class="text-xs text-gray-400 mt-1">{{ run.items_count || 0 }} staff processed</div>

            <div class="mt-4 space-y-2 text-sm border-t pt-3">
              <div class="flex justify-between text-gray-600">
                <span>Gross Pay:</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(run.total_gross) }}</span>
              </div>
              <div class="flex justify-between text-gray-600">
                <span>Deductions:</span>
                <span class="font-medium text-rose-600">- {{ formatCurrency(run.total_deductions) }}</span>
              </div>
              <div class="flex justify-between font-bold text-base text-indigo-600 pt-2 border-t">
                <span>Net Pay:</span>
                <span>{{ formatCurrency(run.total_net) }}</span>
              </div>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t">
            <Link
              :href="route('payroll.show', run.id)"
              class="block w-full text-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-md"
            >
              {{ run.status === 'draft' ? 'Review & Finalize' : 'View Run & Payslips' }} &rarr;
            </Link>
          </div>
        </div>

        <div v-if="runs.data.length === 0" class="col-span-full py-12 text-center text-gray-400 bg-white rounded-lg border border-dashed">
          No payroll cycles calculated yet. Click "Run Monthly Payroll" to calculate this month's salaries.
        </div>
      </div>

      <!-- Run Payroll Modal -->
      <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 space-y-4">
          <h3 class="text-lg font-bold text-gray-900">Run Monthly Payroll</h3>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Month *</label>
              <select v-model="form.month" required class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                <option v-for="m in 12" :key="m" :value="m">{{ monthName(m) }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Year *</label>
              <input v-model="form.year" type="number" required class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
            </div>
            <div class="flex justify-end space-x-3 pt-3 border-t">
              <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md">Cancel</button>
              <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-md">Calculate Run</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
