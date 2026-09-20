<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ChartBarIcon, ArrowTrendingUpIcon, ClockIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  year: Number,
  summary: Object,
  monthly_revenue: Array,
  monthly_payroll: Array,
  ageing: Object,
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
</script>

<template>
  <Head title="Finance & P&L Reports" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Financial Reports & Profitability</h1>
        <p class="text-sm text-gray-500 mt-1">Overview of tuition revenues, payroll expenses, net margins & invoice ageing (Year {{ year }})</p>
      </div>

      <!-- High Level Ribbon -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-lg shadow border border-gray-100">
          <div class="text-xs text-gray-500 font-semibold uppercase">Total Tuition Revenue (YTD)</div>
          <div class="text-2xl font-bold text-emerald-600 mt-1">{{ formatCurrency(summary.total_revenue) }}</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow border border-gray-100">
          <div class="text-xs text-gray-500 font-semibold uppercase">Total Payroll & Staff Costs (YTD)</div>
          <div class="text-2xl font-bold text-rose-600 mt-1">{{ formatCurrency(summary.total_payroll) }}</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow border border-gray-100">
          <div class="text-xs text-gray-500 font-semibold uppercase">Operating Margin</div>
          <div class="text-2xl font-bold mt-1" :class="summary.net_margin >= 0 ? 'text-indigo-600' : 'text-rose-600'">
            {{ formatCurrency(summary.net_margin) }}
          </div>
        </div>
      </div>

      <!-- Monthly Trend Table -->
      <div class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-4">
        <h2 class="text-base font-bold text-gray-900">Monthly Cash Flow Breakdown</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
              <tr>
                <th class="px-4 py-2.5 text-left">Month</th>
                <th class="px-4 py-2.5 text-right">Fee Collections (RM)</th>
                <th class="px-4 py-2.5 text-right">Staff Payroll (RM)</th>
                <th class="px-4 py-2.5 text-right">Net Margin (RM)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="(m, i) in months" :key="m" class="hover:bg-gray-50">
                <td class="px-4 py-2.5 font-bold text-gray-800">{{ m }}</td>
                <td class="px-4 py-2.5 text-right font-medium text-emerald-600">{{ formatCurrency(monthly_revenue[i]) }}</td>
                <td class="px-4 py-2.5 text-right font-medium text-rose-600">{{ formatCurrency(monthly_payroll[i]) }}</td>
                <td class="px-4 py-2.5 text-right font-bold" :class="(monthly_revenue[i] - monthly_payroll[i]) >= 0 ? 'text-gray-900' : 'text-rose-600'">
                  {{ formatCurrency(monthly_revenue[i] - monthly_payroll[i]) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Ageing Analysis Card -->
      <div class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-4">
        <div class="flex items-center space-x-2">
          <ClockIcon class="w-5 h-5 text-indigo-600" />
          <h2 class="text-base font-bold text-gray-900">Outstanding Invoices Ageing Analysis</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200 text-center">
            <div class="text-xs font-bold text-yellow-800 uppercase">1 &ndash; 30 Days Overdue</div>
            <div class="text-2xl font-bold text-yellow-900 mt-1">{{ formatCurrency(ageing.current_to_30) }}</div>
          </div>
          <div class="p-4 bg-orange-50 rounded-lg border border-orange-200 text-center">
            <div class="text-xs font-bold text-orange-800 uppercase">31 &ndash; 60 Days Overdue</div>
            <div class="text-2xl font-bold text-orange-900 mt-1">{{ formatCurrency(ageing.days_31_to_60) }}</div>
          </div>
          <div class="p-4 bg-red-50 rounded-lg border border-red-200 text-center">
            <div class="text-xs font-bold text-red-800 uppercase">60+ Days Overdue</div>
            <div class="text-2xl font-bold text-red-900 mt-1">{{ formatCurrency(ageing.days_60_plus) }}</div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
