<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  UsersIcon, 
  AcademicCapIcon, 
  BanknotesIcon, 
  CreditCardIcon, 
  ExclamationTriangleIcon,
  ArrowTrendingUpIcon
} from '@heroicons/vue/24/outline';

defineProps({
  stats: {
    type: Object,
    default: () => ({
      active_students: 0,
      classes: 0,
      this_month_billed: 0,
      this_month_collected: 0,
      total_outstanding: 0,
      overdue_count: 0
    })
  },
  recent_invoices: {
    type: Array,
    default: () => []
  },
  recent_payments: {
    type: Array,
    default: () => []
  }
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric' });
};

const getStatusBadge = (status) => {
  switch (status) {
    case 'paid':
      return 'bg-green-100 text-green-800';
    case 'partially_paid':
      return 'bg-blue-100 text-blue-800';
    case 'overdue':
      return 'bg-red-100 text-red-800';
    case 'sent':
      return 'bg-yellow-100 text-yellow-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};
</script>

<template>
  <Head title="Admin Dashboard" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <!-- Header with Quick Action -->
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
          <p class="text-sm text-gray-500 mt-1">Kindergarten operations & financial overview</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
          <Link
            :href="route('students.create')"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
          >
            + Enroll Student
          </Link>
          <Link
            :href="route('invoices.create')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            + Create Invoice
          </Link>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Active Students -->
        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
          <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-indigo-50 p-3 rounded-md">
              <UsersIcon class="h-6 w-6 text-indigo-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active Students</dt>
                <dd class="text-2xl font-bold text-gray-900 mt-1">{{ stats.active_students }}</dd>
              </dl>
            </div>
          </div>
        </div>

        <!-- This Month Billed -->
        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
          <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-purple-50 p-3 rounded-md">
              <BanknotesIcon class="h-6 w-6 text-purple-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">This Month Billed</dt>
                <dd class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(stats.this_month_billed) }}</dd>
              </dl>
            </div>
          </div>
        </div>

        <!-- This Month Collected -->
        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
          <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-emerald-50 p-3 rounded-md">
              <CreditCardIcon class="h-6 w-6 text-emerald-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Collected (This Month)</dt>
                <dd class="text-2xl font-bold text-emerald-600 mt-1">{{ formatCurrency(stats.this_month_collected) }}</dd>
              </dl>
            </div>
          </div>
        </div>

        <!-- Total Outstanding -->
        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
          <div class="p-5 flex items-center">
            <div class="flex-shrink-0 bg-rose-50 p-3 rounded-md">
              <ExclamationTriangleIcon class="h-6 w-6 text-rose-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Outstanding</dt>
                <dd class="text-2xl font-bold text-rose-600 mt-1">{{ formatCurrency(stats.total_outstanding) }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Tables Grid: Recent Invoices & Recent Payments -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Invoices -->
        <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Recent Invoices</h2>
            <Link :href="route('invoices.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all &rarr;</Link>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="text-left text-xs font-medium text-gray-500 uppercase py-2">Invoice #</th>
                  <th class="text-left text-xs font-medium text-gray-500 uppercase py-2">Student</th>
                  <th class="text-right text-xs font-medium text-gray-500 uppercase py-2">Total</th>
                  <th class="text-center text-xs font-medium text-gray-500 uppercase py-2">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="inv in recent_invoices" :key="inv.id" class="hover:bg-gray-50">
                  <td class="py-3 text-sm font-medium text-indigo-600">
                    <Link :href="route('invoices.show', inv.id)">{{ inv.invoice_number }}</Link>
                  </td>
                  <td class="py-3 text-sm text-gray-800">{{ inv.student?.name }}</td>
                  <td class="py-3 text-sm text-right font-medium text-gray-900">{{ formatCurrency(inv.total_amount) }}</td>
                  <td class="py-3 text-center">
                    <span :class="['px-2 py-0.5 text-xs font-semibold rounded-full', getStatusBadge(inv.status)]">
                      {{ inv.status.replace('_', ' ') }}
                    </span>
                  </td>
                </tr>
                <tr v-if="recent_invoices.length === 0">
                  <td colspan="4" class="py-4 text-center text-sm text-gray-400">No invoices generated yet.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Recent Payments</h2>
            <Link :href="route('payments.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all &rarr;</Link>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="text-left text-xs font-medium text-gray-500 uppercase py-2">Receipt #</th>
                  <th class="text-left text-xs font-medium text-gray-500 uppercase py-2">Student</th>
                  <th class="text-center text-xs font-medium text-gray-500 uppercase py-2">Method</th>
                  <th class="text-right text-xs font-medium text-gray-500 uppercase py-2">Amount</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="pay in recent_payments" :key="pay.id" class="hover:bg-gray-50">
                  <td class="py-3 text-sm font-medium text-emerald-600">{{ pay.receipt_number || 'RCP-' + pay.id }}</td>
                  <td class="py-3 text-sm text-gray-800">{{ pay.invoice?.student?.name || '-' }}</td>
                  <td class="py-3 text-xs text-center uppercase font-medium text-gray-500">{{ pay.method }}</td>
                  <td class="py-3 text-sm text-right font-bold text-emerald-700">{{ formatCurrency(pay.amount) }}</td>
                </tr>
                <tr v-if="recent_payments.length === 0">
                  <td colspan="4" class="py-4 text-center text-sm text-gray-400">No payments recorded yet.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
