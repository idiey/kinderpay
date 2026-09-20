<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, CheckBadgeIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  run: Object,
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const monthName = (m) => {
  const date = new Date(2026, m - 1, 1);
  return date.toLocaleString('default', { month: 'long' });
};

const confirmRun = () => {
  if (confirm('Confirm and finalize this payroll run? This will lock the figures.')) {
    useForm({}).post(route('payroll.confirm', props.run.id));
  }
};
</script>

<template>
  <Head :title="`Payroll - ${monthName(run.month)} ${run.year}`" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div class="flex items-center space-x-3">
          <Link :href="route('payroll.index')" class="text-gray-500 hover:text-gray-700">
            <ArrowLeftIcon class="w-5 h-5" />
          </Link>
          <div>
            <div class="flex items-center space-x-3">
              <h1 class="text-2xl font-bold text-gray-900">Payroll: {{ monthName(run.month) }} {{ run.year }}</h1>
              <span
                class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase"
                :class="run.status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
              >
                {{ run.status }}
              </span>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">Summary of staff salaries, EPF, SOCSO, EIS & Net Payouts</p>
          </div>
        </div>

        <div v-if="run.status === 'draft'">
          <button
            @click="confirmRun"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700"
          >
            <CheckBadgeIcon class="w-4 h-4 mr-1.5" />
            Confirm & Finalize Payroll
          </button>
        </div>
      </div>

      <!-- Financial Totals -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow border">
          <div class="text-xs text-gray-500 uppercase font-semibold">Total Gross Pay</div>
          <div class="text-xl font-bold text-gray-900 mt-1">{{ formatCurrency(run.total_gross) }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border">
          <div class="text-xs text-gray-500 uppercase font-semibold">Total Deductions</div>
          <div class="text-xl font-bold text-rose-600 mt-1">- {{ formatCurrency(run.total_deductions) }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border">
          <div class="text-xs text-gray-500 uppercase font-semibold">Total Net Payout</div>
          <div class="text-xl font-bold text-indigo-600 mt-1">{{ formatCurrency(run.total_net) }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border">
          <div class="text-xs text-gray-500 uppercase font-semibold">Employer Cost</div>
          <div class="text-xl font-bold text-purple-600 mt-1">{{ formatCurrency(run.total_employer_cost) }}</div>
        </div>
      </div>

      <!-- Staff Breakdown Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
              <tr>
                <th class="px-4 py-3 text-left">Staff Name</th>
                <th class="px-4 py-3 text-right">Basic</th>
                <th class="px-4 py-3 text-right">Allowances</th>
                <th class="px-4 py-3 text-right">Unpaid Ded.</th>
                <th class="px-4 py-3 text-right">Gross</th>
                <th class="px-4 py-3 text-right">EPF (11%)</th>
                <th class="px-4 py-3 text-right">SOCSO/EIS</th>
                <th class="px-4 py-3 text-right">Net Salary</th>
                <th class="px-4 py-3 text-right">Payslip</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="item in run.items" :key="item.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="font-bold text-gray-900">{{ item.staff?.name }}</div>
                  <div class="text-xs text-gray-400">{{ item.staff?.position }}</div>
                </td>
                <td class="px-4 py-3 text-right text-gray-600">{{ formatCurrency(item.basic_salary) }}</td>
                <td class="px-4 py-3 text-right text-gray-600">{{ formatCurrency(item.total_allowances) }}</td>
                <td class="px-4 py-3 text-right text-rose-600">
                  {{ item.unpaid_leave_deduction > 0 ? '-' + formatCurrency(item.unpaid_leave_deduction) : '-' }}
                </td>
                <td class="px-4 py-3 text-right font-medium text-gray-900">{{ formatCurrency(item.adjusted_gross) }}</td>
                <td class="px-4 py-3 text-right text-rose-600">- {{ formatCurrency(item.epf_employee) }}</td>
                <td class="px-4 py-3 text-right text-rose-600">- {{ formatCurrency(Number(item.socso_employee) + Number(item.eis_employee)) }}</td>
                <td class="px-4 py-3 text-right font-bold text-emerald-700">{{ formatCurrency(item.net_pay) }}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <a
                    :href="route('payroll.payslip', item.id)"
                    target="_blank"
                    class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800"
                  >
                    <ArrowDownTrayIcon class="w-3.5 h-3.5 mr-1" />
                    PDF
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
