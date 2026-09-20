<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, PlusIcon, PencilIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  staff: Object,
  allowanceTypes: Array,
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric' });
};

const showAllowanceModal = ref(false);
const allowanceForm = useForm({
  allowance_type_id: props.allowanceTypes?.[0]?.id || '',
  amount: '',
  effective_from: new Date().toISOString().split('T')[0],
});

const addAllowance = () => {
  allowanceForm.post(route('staff.allowances.store', props.staff.id), {
    onSuccess: () => {
      showAllowanceModal.value = false;
      allowanceForm.reset();
    }
  });
};
</script>

<template>
  <Head :title="staff.name" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <Link :href="route('staff.index')" class="text-gray-500 hover:text-gray-700">
            <ArrowLeftIcon class="w-5 h-5" />
          </Link>
          <div>
            <div class="flex items-center space-x-3">
              <h1 class="text-2xl font-bold text-gray-900">{{ staff.name }}</h1>
              <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase bg-green-100 text-green-800">
                {{ staff.status }}
              </span>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">{{ staff.position }} &bull; Joined: {{ formatDate(staff.join_date) }}</p>
          </div>
        </div>

        <Link
          :href="route('staff.edit', staff.id)"
          class="inline-flex items-center px-3.5 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
        >
          <PencilIcon class="w-4 h-4 mr-1.5" />
          Edit Profile
        </Link>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Particulars -->
        <div class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-4">
          <h2 class="text-base font-semibold text-gray-900 border-b pb-2">Employment Details</h2>
          <dl class="space-y-2 text-sm">
            <div>
              <dt class="text-xs text-gray-500 uppercase">Employee ID</dt>
              <dd class="font-medium text-gray-900">{{ staff.employee_id || 'None' }}</dd>
            </div>
            <div>
              <dt class="text-xs text-gray-500 uppercase">Type</dt>
              <dd class="font-medium text-gray-900 capitalize">{{ staff.employment_type?.replace('_', ' ') }}</dd>
            </div>
            <div>
              <dt class="text-xs text-gray-500 uppercase">Basic Monthly Salary</dt>
              <dd class="text-lg font-bold text-indigo-600">{{ formatCurrency(staff.basic_salary) }}</dd>
            </div>
            <div>
              <dt class="text-xs text-gray-500 uppercase">Contact</dt>
              <dd class="text-gray-900">{{ staff.phone }}</dd>
              <dd class="text-xs text-gray-500">{{ staff.email }}</dd>
            </div>
            <div>
              <dt class="text-xs text-gray-500 uppercase">Statutory & Bank</dt>
              <dd class="text-xs text-gray-700 mt-1">Bank: {{ staff.bank_name || 'N/A' }} ({{ staff.bank_account || 'N/A' }})</dd>
              <dd class="text-xs text-gray-700">EPF: {{ staff.epf_number || 'N/A' }} | SOCSO: {{ staff.socso_number || 'N/A' }}</dd>
            </div>
          </dl>
        </div>

        <!-- Allowances & Leaves -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Allowances Card -->
          <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-base font-semibold text-gray-900">Fixed Monthly Allowances</h2>
              <button
                @click="showAllowanceModal = true"
                class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800"
              >
                <PlusIcon class="w-4 h-4 mr-1" />
                Add Allowance
              </button>
            </div>

            <div class="divide-y divide-gray-100 text-sm">
              <div v-for="a in staff.allowances" :key="a.id" class="py-2.5 flex justify-between items-center">
                <div>
                  <div class="font-medium text-gray-900">{{ a.allowance_type?.name }}</div>
                  <div class="text-xs text-gray-400">Effective: {{ formatDate(a.effective_from) }}</div>
                </div>
                <div class="font-bold text-gray-900">{{ formatCurrency(a.amount) }}</div>
              </div>

              <div v-if="!staff.allowances || staff.allowances.length === 0" class="py-4 text-center text-xs text-gray-400">
                No fixed allowances configured.
              </div>
            </div>
          </div>

          <!-- Leave Balances -->
          <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Leave Balances</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div v-for="b in staff.leave_balances" :key="b.id" class="bg-gray-50 p-3 rounded-lg border text-center">
                <div class="text-xs font-bold text-gray-500 uppercase">{{ b.leave_type?.code || 'Leave' }}</div>
                <div class="text-xl font-extrabold text-indigo-600 mt-1">{{ b.balance }}</div>
                <div class="text-xs text-gray-400">Used: {{ b.used }} / {{ b.entitled }}</div>
              </div>
              <div v-if="!staff.leave_balances || staff.leave_balances.length === 0" class="col-span-full text-center text-xs text-gray-400 py-3">
                No leave records tracked yet.
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add Allowance Modal -->
      <div v-if="showAllowanceModal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 space-y-4">
          <h3 class="text-lg font-bold text-gray-900">Add Staff Allowance</h3>
          <form @submit.prevent="addAllowance" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Allowance Type *</label>
              <select v-model="allowanceForm.allowance_type_id" required class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                <option v-for="at in allowanceTypes" :key="at.id" :value="at.id">{{ at.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Monthly Amount (RM) *</label>
              <input v-model="allowanceForm.amount" type="number" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 text-sm font-bold" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Effective From *</label>
              <input v-model="allowanceForm.effective_from" type="date" required class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
            </div>
            <div class="flex justify-end space-x-3 pt-3 border-t">
              <button type="button" @click="showAllowanceModal = false" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md">Cancel</button>
              <button type="submit" :disabled="allowanceForm.processing" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-md">Save Allowance</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
