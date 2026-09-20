<script setup>
import ParentLayout from '@/Layouts/ParentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { AcademicCapIcon, CreditCardIcon, CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  students: Array,
  recent_invoices: Array,
  outstanding_balance: Number,
  total_paid: Number,
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric' });
};
</script>

<template>
  <Head title="Parent Portal - Dashboard" />

  <ParentLayout>
    <div class="space-y-6">
      <!-- Welcome Header -->
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Parent Portal</h1>
        <p class="text-sm text-gray-500 mt-1">View child particulars, tuition statements, and pay bills online via FPX</p>
      </div>

      <!-- Outstanding Alert / Banner -->
      <div
        v-if="outstanding_balance > 0"
        class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm flex items-center justify-between"
      >
        <div class="flex items-center space-x-3">
          <ExclamationCircleIcon class="w-6 h-6 text-amber-600 flex-shrink-0" />
          <div>
            <h3 class="text-sm font-bold text-amber-900">Payment Due Reminder</h3>
            <p class="text-xs text-amber-700 mt-0.5">
              You have an outstanding balance of <strong>{{ formatCurrency(outstanding_balance) }}</strong>.
            </p>
          </div>
        </div>
        <Link
          :href="route('parent.invoices')"
          class="px-4 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-md shadow-sm"
        >
          View & Settle &rarr;
        </Link>
      </div>
      <div v-else class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center space-x-3">
        <CheckCircleIcon class="w-6 h-6 text-emerald-600 flex-shrink-0" />
        <div>
          <h3 class="text-sm font-bold text-emerald-900">All Accounts Settled</h3>
          <p class="text-xs text-emerald-700 mt-0.5">You have no outstanding fees at this time. Thank you!</p>
        </div>
      </div>

      <!-- Overview Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-white p-5 rounded-lg shadow border border-gray-100 flex items-center">
          <div class="p-3 bg-amber-50 rounded-lg text-amber-600 mr-4">
            <CreditCardIcon class="w-6 h-6" />
          </div>
          <div>
            <div class="text-xs font-medium text-gray-500 uppercase">Outstanding Balance</div>
            <div class="text-2xl font-bold mt-0.5" :class="outstanding_balance > 0 ? 'text-rose-600' : 'text-emerald-600'">
              {{ formatCurrency(outstanding_balance) }}
            </div>
          </div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow border border-gray-100 flex items-center">
          <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600 mr-4">
            <CheckCircleIcon class="w-6 h-6" />
          </div>
          <div>
            <div class="text-xs font-medium text-gray-500 uppercase">Total Paid to Date</div>
            <div class="text-2xl font-bold text-gray-900 mt-0.5">{{ formatCurrency(total_paid) }}</div>
          </div>
        </div>
      </div>

      <!-- Registered Children -->
      <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
        <h2 class="text-base font-bold text-gray-900 mb-4">My Children Enrolled</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="s in students"
            :key="s.id"
            class="p-4 bg-gray-50 rounded-lg border border-gray-200 flex items-center space-x-3"
          >
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
              {{ s.name.charAt(0) }}
            </div>
            <div>
              <div class="font-bold text-gray-900 text-sm">{{ s.name }}</div>
              <div class="text-xs text-gray-500">Class: {{ s.class_group?.name || 'Unassigned' }}</div>
            </div>
          </div>

          <div v-if="students.length === 0" class="col-span-full py-6 text-center text-sm text-gray-400">
            No children records currently linked to this parent account. Please contact school administration.
          </div>
        </div>
      </div>

      <!-- Recent Invoices -->
      <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-base font-bold text-gray-900">Recent Invoices</h2>
          <Link :href="route('parent.invoices')" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
            View all invoices &rarr;
          </Link>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
              <tr>
                <th class="px-4 py-2.5 text-left">Invoice #</th>
                <th class="px-4 py-2.5 text-left">Child</th>
                <th class="px-4 py-2.5 text-left">Month</th>
                <th class="px-4 py-2.5 text-right">Total</th>
                <th class="px-4 py-2.5 text-right">Balance Due</th>
                <th class="px-4 py-2.5 text-center">Status</th>
                <th class="px-4 py-2.5 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="inv in recent_invoices" :key="inv.id">
                <td class="px-4 py-3 font-medium text-emerald-700">
                  <Link :href="route('parent.invoices.show', inv.id)">{{ inv.invoice_number }}</Link>
                </td>
                <td class="px-4 py-3 text-gray-800">{{ inv.student?.name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ formatDate(inv.billing_month) }}</td>
                <td class="px-4 py-3 text-right font-medium text-gray-900">{{ formatCurrency(inv.total_amount) }}</td>
                <td class="px-4 py-3 text-right font-bold" :class="inv.balance_due > 0 ? 'text-rose-600' : 'text-emerald-600'">
                  {{ formatCurrency(inv.balance_due) }}
                </td>
                <td class="px-4 py-3 text-center">
                  <span
                    class="px-2 py-0.5 text-xs font-semibold rounded-full uppercase"
                    :class="inv.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                  >
                    {{ inv.status.replace('_', ' ') }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right">
                  <Link
                    :href="route('parent.invoices.show', inv.id)"
                    class="text-xs font-semibold text-emerald-600 hover:text-emerald-800"
                  >
                    {{ inv.balance_due > 0 ? 'Pay Now' : 'View' }} &rarr;
                  </Link>
                </td>
              </tr>
              <tr v-if="recent_invoices.length === 0">
                <td colspan="7" class="px-4 py-6 text-center text-gray-400">
                  No invoices issued yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </ParentLayout>
</template>
