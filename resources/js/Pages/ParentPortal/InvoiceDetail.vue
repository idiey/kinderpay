<script setup>
import { ref } from 'vue';
import ParentLayout from '@/Layouts/ParentLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, ArrowDownTrayIcon, CreditCardIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  invoice: Object,
});

const isProcessing = ref(false);

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric' });
};

const payOnline = () => {
  isProcessing.value = true;
  useForm({}).post(route('parent.invoices.pay', props.invoice.id), {
    onFinish: () => {
      isProcessing.value = false;
    }
  });
};
</script>

<template>
  <Head :title="'Invoice ' + invoice.invoice_number" />

  <ParentLayout>
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center space-x-4">
        <Link :href="route('parent.invoices')" class="text-gray-500 hover:text-gray-700">
          <ArrowLeftIcon class="w-5 h-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Invoice Details</h1>
          <p class="text-sm text-gray-500 mt-0.5">{{ invoice.invoice_number }} &bull; {{ invoice.student?.name }}</p>
        </div>
      </div>

      <!-- Payment CTA Box if Balance Due > 0 -->
      <div
        v-if="invoice.balance_due > 0"
        class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-lg p-6 shadow-md flex flex-col sm:flex-row items-center justify-between gap-4"
      >
        <div>
          <div class="text-xs uppercase tracking-wider text-emerald-100 font-semibold">Outstanding Balance Due</div>
          <div class="text-3xl font-extrabold mt-1">{{ formatCurrency(invoice.balance_due) }}</div>
          <div class="text-xs text-emerald-100 mt-1">Due Date: {{ formatDate(invoice.due_date) }}</div>
        </div>

        <div>
          <button
            @click="payOnline"
            :disabled="isProcessing"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-bold rounded-md shadow-sm text-emerald-800 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition"
          >
            <CreditCardIcon class="w-5 h-5 mr-2 text-emerald-600" />
            {{ isProcessing ? 'Connecting to FPX...' : 'Pay with FPX Online' }}
          </button>
          <div class="text-xs text-center text-emerald-100 mt-1.5 flex items-center justify-center space-x-1">
            <span>Maybank2u, CIMB Clicks, RHB, etc.</span>
          </div>
        </div>
      </div>
      <div v-else class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 flex items-center space-x-3 text-emerald-800">
        <CheckCircleIcon class="w-6 h-6 text-emerald-600 flex-shrink-0" />
        <div class="text-sm font-semibold">
          This invoice has been settled in full. Thank you!
        </div>
      </div>

      <!-- Invoice Statement Card -->
      <div class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-6">
        <div class="flex items-center justify-between border-b pb-4">
          <div>
            <span class="text-xs text-gray-500 uppercase font-bold">Child Name:</span>
            <div class="text-lg font-bold text-gray-900">{{ invoice.student?.name }}</div>
            <div class="text-sm text-gray-600">Class: {{ invoice.student?.class_group?.name || 'Unassigned' }}</div>
          </div>
          <div class="text-right">
            <a
              :href="route('invoices.pdf', invoice.id)"
              target="_blank"
              class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-800 border border-emerald-200 px-3 py-1.5 rounded-md"
            >
              <ArrowDownTrayIcon class="w-4 h-4 mr-1" />
              Download Statement PDF
            </a>
          </div>
        </div>

        <!-- Items Table -->
        <div>
          <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Item Breakdown</h3>
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead>
              <tr class="bg-gray-50 text-xs text-gray-500 uppercase">
                <th class="px-4 py-2 text-left">Description</th>
                <th class="px-4 py-2 text-right">Qty</th>
                <th class="px-4 py-2 text-right">Rate</th>
                <th class="px-4 py-2 text-right">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="item in invoice.items" :key="item.id">
                <td class="px-4 py-3 font-medium text-gray-900">{{ item.description }}</td>
                <td class="px-4 py-3 text-right text-gray-600">{{ item.quantity }}</td>
                <td class="px-4 py-3 text-right text-gray-600">{{ formatCurrency(item.unit_price) }}</td>
                <td class="px-4 py-3 text-right font-bold text-gray-900">{{ formatCurrency(item.total) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Totals Breakdown -->
        <div class="flex justify-end pt-4 border-t">
          <div class="w-64 space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
              <span>Invoice Total:</span>
              <span class="font-bold text-gray-900">{{ formatCurrency(invoice.total_amount) }}</span>
            </div>
            <div class="flex justify-between text-emerald-600">
              <span>Paid to Date:</span>
              <span class="font-bold">{{ formatCurrency(invoice.paid_amount) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold pt-2 border-t" :class="invoice.balance_due > 0 ? 'text-rose-600' : 'text-emerald-600'">
              <span>Remaining Balance:</span>
              <span>{{ formatCurrency(invoice.balance_due) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Receipts list for payments made toward this invoice -->
      <div v-if="invoice.payments && invoice.payments.length > 0" class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-4">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Completed Payments & Receipts</h3>
        <div class="divide-y divide-gray-100">
          <div v-for="pay in invoice.payments" :key="pay.id" class="py-3 flex items-center justify-between text-sm">
            <div>
              <div class="font-bold text-emerald-700">{{ pay.receipt_number || 'Receipt #' + pay.id }}</div>
              <div class="text-xs text-gray-500">{{ formatDate(pay.paid_at) }} &bull; Method: {{ pay.method?.toUpperCase() }}</div>
            </div>
            <div class="flex items-center space-x-4">
              <span class="font-bold text-gray-900">{{ formatCurrency(pay.amount) }}</span>
              <a
                :href="route('parent.payments.receipt', pay.id)"
                target="_blank"
                class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-800"
              >
                <ArrowDownTrayIcon class="w-4 h-4 mr-1" />
                Receipt PDF
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ParentLayout>
</template>
