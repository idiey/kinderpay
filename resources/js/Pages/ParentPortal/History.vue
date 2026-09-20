<script setup>
import ParentLayout from '@/Layouts/ParentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  payments: Object,
});

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
  <Head title="Payment History & Receipts" />

  <ParentLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Payment History</h1>
        <p class="text-sm text-gray-500 mt-1">Download official receipts for employer claims and tax deductions</p>
      </div>

      <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Receipt #</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Child</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Invoice Ref</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Method</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date Paid</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Amount Paid</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="p in payments.data" :key="p.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap font-bold text-emerald-700">
                  {{ p.receipt_number || 'RCP-' + p.id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                  {{ p.invoice?.student?.name || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600">
                  <Link v-if="p.invoice" :href="route('parent.invoices.show', p.invoice.id)">
                    {{ p.invoice.invoice_number }}
                  </Link>
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
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <a
                    :href="route('parent.payments.receipt', p.id)"
                    target="_blank"
                    class="inline-flex items-center px-3 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-md border border-emerald-200"
                  >
                    <ArrowDownTrayIcon class="w-3.5 h-3.5 mr-1" />
                    Download Receipt PDF
                  </a>
                </td>
              </tr>
              <tr v-if="payments.data.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
                  No payment history found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="payments.links && payments.links.length > 3" class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
          <div class="text-xs text-gray-500">
            Showing {{ payments.from || 0 }} to {{ payments.to || 0 }} of {{ payments.total }} records
          </div>
          <div class="flex space-x-1">
            <template v-for="(link, i) in payments.links" :key="i">
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
