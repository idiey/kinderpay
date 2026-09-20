<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { 
  MagnifyingGlassIcon, 
  PlusIcon, 
  ArrowPathIcon, 
  ArrowDownTrayIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
  invoices: Object,
  filters: Object,
  stats: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const month = ref(props.filters.month || '');

const applyFilters = () => {
  router.get(route('invoices.index'), {
    search: search.value || undefined,
    status: status.value || undefined,
    month: month.value || undefined,
  }, {
    preserveState: true,
    replace: true,
  });
};

watch([status, month], () => {
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
  return d.toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric' });
};

const getStatusBadge = (st) => {
  switch (st) {
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

const isGenerating = ref(false);
const generateMonth = ref(new Date().toISOString().substring(0, 7)); // YYYY-MM

const generateInvoices = () => {
  if (confirm(`Generate recurring monthly invoices for ${generateMonth.value}?`)) {
    isGenerating.value = true;
    router.post(route('invoices.generate'), {
      month: generateMonth.value,
    }, {
      onFinish: () => {
        isGenerating.value = false;
      }
    });
  }
};
</script>

<template>
  <Head title="Invoices" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Invoices & Billing</h1>
          <p class="text-sm text-gray-500 mt-1">Manage monthly tuition invoices, fee statements, and billing cycles</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
          <div class="flex items-center space-x-2">
            <input
              v-model="generateMonth"
              type="month"
              class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5"
            />
            <button
              @click="generateInvoices"
              :disabled="isGenerating"
              class="inline-flex items-center px-3.5 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50"
            >
              <ArrowPathIcon class="w-4 h-4 mr-1.5" :class="{ 'animate-spin': isGenerating }" />
              Generate Month Invoices
            </button>
          </div>
          <Link
            :href="route('invoices.create')"
            class="inline-flex items-center px-3.5 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
          >
            <PlusIcon class="w-4 h-4 mr-1.5" />
            + Ad-hoc Invoice
          </Link>
        </div>
      </div>

      <!-- Stats Ribbon -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-lg shadow border border-gray-100 flex justify-between items-center">
          <div>
            <div class="text-xs text-gray-500 uppercase font-semibold">Total Invoiced</div>
            <div class="text-xl font-bold text-gray-900 mt-1">{{ formatCurrency(stats.total_billed) }}</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-100 flex justify-between items-center">
          <div>
            <div class="text-xs text-gray-500 uppercase font-semibold">Total Collected</div>
            <div class="text-xl font-bold text-emerald-600 mt-1">{{ formatCurrency(stats.total_paid) }}</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border border-gray-100 flex justify-between items-center">
          <div>
            <div class="text-xs text-gray-500 uppercase font-semibold">Outstanding Balance</div>
            <div class="text-xl font-bold text-rose-600 mt-1">{{ formatCurrency(stats.total_outstanding) }}</div>
          </div>
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
            placeholder="Search invoice number or student name..."
            class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        <div class="w-full sm:w-44">
          <input
            v-model="month"
            type="month"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        <div class="w-full sm:w-40">
          <select
            v-model="status"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">All Statuses</option>
            <option value="draft">Draft</option>
            <option value="sent">Sent</option>
            <option value="partially_paid">Partially Paid</option>
            <option value="paid">Paid</option>
            <option value="overdue">Overdue</option>
          </select>
        </div>
      </div>

      <!-- Invoices Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Invoice #</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Student</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Month</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Balance Due</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="inv in invoices.data" :key="inv.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link :href="route('invoices.show', inv.id)" class="font-bold text-indigo-600 hover:text-indigo-900">
                    {{ inv.invoice_number }}
                  </Link>
                  <div class="text-xs text-gray-400">Due: {{ formatDate(inv.due_date) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-gray-900">{{ inv.student?.name }}</div>
                  <div class="text-xs text-gray-400">{{ inv.student?.class_group?.name || 'No Class' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ formatDate(inv.billing_month) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                  {{ formatCurrency(inv.total_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold" :class="inv.balance_due > 0 ? 'text-rose-600' : 'text-emerald-600'">
                  {{ formatCurrency(inv.balance_due) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span :class="['px-2.5 py-1 text-xs font-semibold rounded-full uppercase', getStatusBadge(inv.status)]">
                    {{ inv.status.replace('_', ' ') }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <Link :href="route('invoices.show', inv.id)" class="text-indigo-600 hover:text-indigo-900">
                    View
                  </Link>
                  <a
                    :href="route('invoices.pdf', inv.id)"
                    target="_blank"
                    class="text-gray-500 hover:text-gray-700"
                    title="Download PDF"
                  >
                    PDF
                  </a>
                </td>
              </tr>
              <tr v-if="invoices.data.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
                  No invoices found. Use "Generate Month Invoices" to create billing for all enrolled students.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="invoices.links && invoices.links.length > 3" class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
          <div class="text-xs text-gray-500">
            Showing {{ invoices.from || 0 }} to {{ invoices.to || 0 }} of {{ invoices.total }} invoices
          </div>
          <div class="flex space-x-1">
            <template v-for="(link, i) in invoices.links" :key="i">
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
