<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  invoice: Object,
});

const form = useForm({
  amount: props.invoice.balance_due,
  method: 'cash',
  paid_at: new Date().toISOString().split('T')[0],
  reference_number: '',
  notes: '',
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const submit = () => {
  form.post(route('payments.store', props.invoice.id));
};
</script>

<template>
  <Head :title="'Record Payment - ' + invoice.invoice_number" />

  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto space-y-6">
      <div class="flex items-center space-x-4">
        <Link :href="route('invoices.show', invoice.id)" class="text-gray-500 hover:text-gray-700">
          <ArrowLeftIcon class="w-5 h-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Record Payment</h1>
          <p class="text-sm text-gray-500 mt-0.5">Enter manual payment receipt for invoice {{ invoice.invoice_number }}</p>
        </div>
      </div>

      <!-- Invoice Context Summary Card -->
      <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 flex justify-between items-center text-sm">
        <div>
          <div class="text-xs text-indigo-500 font-semibold uppercase">Student</div>
          <div class="font-bold text-gray-900 text-base">{{ invoice.student?.name }}</div>
          <div class="text-xs text-gray-500">Class: {{ invoice.student?.class_group?.name || 'Unassigned' }}</div>
        </div>
        <div class="text-right">
          <div class="text-xs text-indigo-500 font-semibold uppercase">Outstanding Balance</div>
          <div class="text-2xl font-bold text-rose-600">{{ formatCurrency(invoice.balance_due) }}</div>
        </div>
      </div>

      <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-6">
        <div class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Payment Amount (RM) *</label>
            <input
              v-model="form.amount"
              type="number"
              step="0.01"
              :max="invoice.balance_due"
              min="0.01"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg font-bold"
            />
            <span v-if="form.errors.amount" class="text-xs text-red-600 mt-1">{{ form.errors.amount }}</span>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Payment Method *</label>
            <select
              v-model="form.method"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="cash">Cash / Tunai</option>
              <option value="bank_transfer">Direct Bank Transfer / CDM</option>
              <option value="cheque">Cheque / Cek</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Date of Payment *</label>
            <input
              v-model="form.paid_at"
              type="date"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Bank Ref / Slip No. / Cheque No.</label>
            <input
              v-model="form.reference_number"
              type="text"
              placeholder="e.g. MBB-128938192 or CDM Slip #29"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Remarks / Notes</label>
            <textarea
              v-model="form.notes"
              rows="2"
              placeholder="e.g. Paid in cash at administration desk"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            ></textarea>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
          <Link
            :href="route('invoices.show', invoice.id)"
            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-md hover:bg-emerald-700 shadow-sm"
          >
            Confirm & Record Payment
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
