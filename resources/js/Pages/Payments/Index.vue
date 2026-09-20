<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { MagnifyingGlassIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  payments: Object,
  filters: Object,
  total_collected: Number,
});

const search = ref(props.filters.search || '');
const method = ref(props.filters.method || '');
const status = ref(props.filters.status || '');

const applyFilters = () => {
  router.get(route('payments.index'), {
    search: search.value || undefined,
    method: method.value || undefined,
    status: status.value || undefined,
  }, {
    preserveState: true,
    replace: true,
  });
};

watch([method, status], () => {
  applyFilters();
});

let debounceTimer = null;
const handleSearch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => applyFilters(), 400);
};

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
  <Head title="Payments Collection" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Payments & Collections</h1>
          <p class="text-sm text-gray-500 mt-1">Audit trail of online FPX collections and manual cash/bank transfers</p>
        </div>
        <div class="mt-4 sm:mt-0 bg-emerald-50 px-4 py-2.5 rounded-lg border border-emerald-100 flex items-center space-x-3">
          <div class="text-xs font-semibold text-emerald-800 uppercase">Filtered Total:</div>
          <div class="text-xl font-bold text-emerald-700">{{ formatCurrency(total_collected) }}</div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white p-4 rounded-lg shadow border border-gray-100 flex flex-col sm:flex-row gap-3 items-center">
        <div class="relative flex-1 w-full">
          <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" />
          <input
            v-model="search"
            @input="handleSearch"
            type="text"
            placeholder="Search by receipt #, ref #, or student name..."
            class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        <div class="w-full sm:w-44">
          <select
            v-model="method"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">All Methods</option>
            <option value="fpx">FPX Online</option>
            <option value="card">Credit/Debit Card</option>
            <option value="cash">Cash</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="cheque">Cheque</option>
          </select>
        </div>
        <div class="w-full sm:w-40">
          <select
            v-model="status"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">All Statuses</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
            <option value="failed">Failed</option>
          </select>
        </div>
      </div>

      <!-- Payments Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Receipt #</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Student & Class</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Invoice #</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Method</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date Paid</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="p in payments.data" :key="p.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap font-bold text-emerald-700">
                  {{ p.receipt_number || 'RCP-' + p.id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-gray-900">{{ p.invoice?.student?.name || '-' }}</div>
                  <div class="text-xs text-gray-400">{{ p.invoice?.student?.class_group?.name || 'Unassigned' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                  <Link v-if="p.invoice" :href="route('invoices.show', p.invoice.id)">{{ p.invoice.invoice_number }}</Link>
                  <span v-else>-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase bg-gray-100 text-gray-700">
                    {{ p.method }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ formatDate(p.paid_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-gray-900">
                  {{ formatCurrency(p.amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span
                    class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase"
                    :class="p.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                  >
                    {{ p.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <a
                    :href="route('payments.receipt', p.id)"
                    target="_blank"
                    class="inline-flex items-center text-indigo-600 hover:text-indigo-900"
                  >
                    <ArrowDownTrayIcon class="w-4 h-4 mr-1" />
                    Receipt
                  </a>
                </td>
              </tr>
              <tr v-if="payments.data.length === 0">
                <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-400">
                  No payments recorded matching filters.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="payments.links && payments.links.length > 3" class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
          <div class="text-xs text-gray-500">
            Showing {{ payments.from || 0 }} to {{ payments.to || 0 }} of {{ payments.total }} payments
          </div>
          <div class="flex space-x-1">
            <template v-for="(link, i) in payments.links" :key="i">
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
