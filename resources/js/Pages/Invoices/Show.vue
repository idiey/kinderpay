<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
  ArrowLeftIcon, 
  ArrowDownTrayIcon, 
  PaperAirplaneIcon, 
  CreditCardIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
  invoice: Object,
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

const sendInvoice = () => {
  if (confirm('Mark this invoice as sent to parent?')) {
    useForm({}).post(route('invoices.send', props.invoice.id));
  }
};
</script>

<template>
  <Head :title="'Invoice ' + invoice.invoice_number" />

  <AuthenticatedLayout>
    <div class="max-w-5xl mx-auto space-y-6">
      <!-- Top Bar -->
      <div class="sm:flex sm:items-center sm:justify-between">
        <div class="flex items-center space-x-3">
          <Link :href="route('invoices.index')" class="text-gray-500 hover:text-gray-700">
            <ArrowLeftIcon class="w-5 h-5" />
          </Link>
          <div>
            <div class="flex items-center space-x-3">
              <h1 class="text-2xl font-bold text-gray-900">{{ invoice.invoice_number }}</h1>
              <span :class="['px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase', getStatusBadge(invoice.status)]">
                {{ invoice.status.replace('_', ' ') }}
              </span>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">
              Billing Month: {{ formatDate(invoice.billing_month) }} &bull; Due: {{ formatDate(invoice.due_date) }}
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
          <a
            :href="route('invoices.pdf', invoice.id)"
            target="_blank"
            class="inline-flex items-center px-3.5 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            <ArrowDownTrayIcon class="w-4 h-4 mr-1.5" />
            Download PDF
          </a>

          <button
            v-if="invoice.status === 'draft'"
            @click="sendInvoice"
            class="inline-flex items-center px-3.5 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            <PaperAirplaneIcon class="w-4 h-4 mr-1.5 text-indigo-600" />
            Mark as Sent
          </button>

          <Link
            v-if="invoice.balance_due > 0"
            :href="route('payments.record', invoice.id)"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700"
          >
            <CreditCardIcon class="w-4 h-4 mr-1.5" />
            Record Payment
          </Link>
        </div>
      </div>

      <!-- Main Invoice View Card -->
      <div class="bg-white shadow rounded-lg p-6 sm:p-8 border border-gray-100 space-y-6">
        <!-- Meta Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-b pb-6">
          <div>
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Student & Guardian</h3>
            <div class="font-bold text-lg text-gray-900">{{ invoice.student?.name }}</div>
            <div class="text-sm text-gray-600">Class: {{ invoice.student?.class_group?.name || 'Unassigned' }}</div>
            <div v-if="invoice.student?.guardians?.[0]" class="text-sm text-gray-600 mt-1">
              Guardian: {{ invoice.student.guardians[0].name }} ({{ invoice.student.guardians[0].relationship }})<br>
              Phone: {{ invoice.student.guardians[0].phone }}
            </div>
          </div>

          <div class="sm:text-right space-y-1">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Payment Status</h3>
            <div class="text-sm text-gray-600">Invoice Total: <strong class="text-gray-900">{{ formatCurrency(invoice.total_amount) }}</strong></div>
            <div class="text-sm text-emerald-600">Amount Paid: <strong>{{ formatCurrency(invoice.paid_amount) }}</strong></div>
            <div class="text-lg font-bold" :class="invoice.balance_due > 0 ? 'text-rose-600' : 'text-emerald-600'">
              Balance Due: {{ formatCurrency(invoice.balance_due) }}
            </div>
          </div>
        </div>

        <!-- Items Table -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Item Breakdown</h3>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Item Description</th>
                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Qty</th>
                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Unit Price</th>
                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Discount</th>
                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="item in invoice.items" :key="item.id">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ item.description }}</td>
                <td class="px-4 py-3 text-sm text-right text-gray-600">{{ item.quantity }}</td>
                <td class="px-4 py-3 text-sm text-right text-gray-600">{{ formatCurrency(item.unit_price) }}</td>
                <td class="px-4 py-3 text-sm text-right text-emerald-600">- {{ formatCurrency(item.discount) }}</td>
                <td class="px-4 py-3 text-sm text-right font-bold text-gray-900">{{ formatCurrency(item.total) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Totals Summary -->
        <div class="flex justify-end pt-4 border-t">
          <div class="w-72 space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
              <span>Subtotal:</span>
              <span>{{ formatCurrency(invoice.subtotal) }}</span>
            </div>
            <div v-if="invoice.discount_total > 0" class="flex justify-between text-emerald-600">
              <span>Total Discounts:</span>
              <span>- {{ formatCurrency(invoice.discount_total) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t">
              <span>Invoice Total:</span>
              <span>{{ formatCurrency(invoice.total_amount) }}</span>
            </div>
            <div class="flex justify-between text-emerald-600">
              <span>Total Paid:</span>
              <span>{{ formatCurrency(invoice.paid_amount) }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold pt-2 border-t" :class="invoice.balance_due > 0 ? 'text-rose-600' : 'text-emerald-600'">
              <span>Balance Due:</span>
              <span>{{ formatCurrency(invoice.balance_due) }}</span>
            </div>
          </div>
        </div>

        <!-- Notes if any -->
        <div v-if="invoice.notes" class="bg-gray-50 p-4 rounded-md border text-sm text-gray-600">
          <strong class="font-semibold text-gray-700">Remarks / Notes:</strong> {{ invoice.notes }}
        </div>
      </div>

      <!-- Payment History Card -->
      <div class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-4">
        <h2 class="text-base font-bold text-gray-900">Payment Transactions</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
              <tr>
                <th class="px-4 py-2 text-left">Receipt #</th>
                <th class="px-4 py-2 text-left">Date</th>
                <th class="px-4 py-2 text-center">Method</th>
                <th class="px-4 py-2 text-left">Reference</th>
                <th class="px-4 py-2 text-right">Amount</th>
                <th class="px-4 py-2 text-center">Status</th>
                <th class="px-4 py-2 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="pay in invoice.payments" :key="pay.id">
                <td class="px-4 py-2.5 font-medium text-emerald-600">{{ pay.receipt_number || 'RCP-' + pay.id }}</td>
                <td class="px-4 py-2.5 text-gray-600">{{ formatDate(pay.paid_at) }}</td>
                <td class="px-4 py-2.5 text-center uppercase text-xs font-semibold text-gray-500">{{ pay.method }}</td>
                <td class="px-4 py-2.5 text-gray-500">{{ pay.reference_number || pay.gateway_ref || '-' }}</td>
                <td class="px-4 py-2.5 text-right font-bold text-gray-900">{{ formatCurrency(pay.amount) }}</td>
                <td class="px-4 py-2.5 text-center">
                  <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 uppercase">
                    {{ pay.status }}
                  </span>
                </td>
                <td class="px-4 py-2.5 text-right">
                  <a
                    :href="route('payments.receipt', pay.id)"
                    target="_blank"
                    class="text-indigo-600 hover:text-indigo-900 text-xs font-medium"
                  >
                    Receipt PDF
                  </a>
                </td>
              </tr>
              <tr v-if="!invoice.payments || invoice.payments.length === 0">
                <td colspan="7" class="px-4 py-6 text-center text-gray-400">
                  No payment transactions recorded for this invoice yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
