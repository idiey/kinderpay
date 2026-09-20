<script setup>
import ParentLayout from '@/Layouts/ParentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  invoices: Object,
});

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
</script>

<template>
  <Head title="Parent Portal - My Invoices" />

  <ParentLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Tuition Invoices</h1>
        <p class="text-sm text-gray-500 mt-1">Review all tuition statements and settle balances securely online</p>
      </div>

      <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Invoice #</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Child Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Billing Month</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Due Date</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Amount</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Balance Due</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="inv in invoices.data" :key="inv.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap font-bold text-emerald-700">
                  <Link :href="route('parent.invoices.show', inv.id)">{{ inv.invoice_number }}</Link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-gray-900">{{ inv.student?.name }}</div>
                  <div class="text-xs text-gray-400">{{ inv.student?.class_group?.name || 'Unassigned' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ formatDate(inv.billing_month) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ formatDate(inv.due_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                  {{ formatCurrency(inv.total_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold" :class="inv.balance_due > 0 ? 'text-rose-600' : 'text-emerald-600'">
                  {{ formatCurrency(inv.balance_due) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span :class="['px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase', getStatusBadge(inv.status)]">
                    {{ inv.status.replace('_', ' ') }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <Link
                    :href="route('parent.invoices.show', inv.id)"
                    class="inline-flex items-center px-3 py-1 rounded border border-transparent text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700"
                  >
                    {{ inv.balance_due > 0 ? 'Pay Online' : 'View Details' }}
                  </Link>
                  <a
                    :href="route('invoices.pdf', inv.id)"
                    target="_blank"
                    class="text-gray-500 hover:text-gray-700 text-xs"
                    title="Download Statement PDF"
                  >
                    PDF
                  </a>
                </td>
              </tr>
              <tr v-if="invoices.data.length === 0">
                <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-400">
                  No invoices currently available.
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
                :class="[link.active ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100', 'px-3 py-1 text-xs rounded border border-gray-300']"
                v-html="link.label"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </ParentLayout>
</template>
