<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  student: Object,
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric' });
};

const deleteStudent = () => {
  if (confirm(`Are you sure you want to delete ${props.student.name}? This will remove associated records.`)) {
    useForm({}).delete(route('students.destroy', props.student.id));
  }
};
</script>

<template>
  <Head :title="student.name" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <Link :href="route('students.index')" class="text-gray-500 hover:text-gray-700">
            <ArrowLeftIcon class="w-5 h-5" />
          </Link>
          <div>
            <div class="flex items-center space-x-3">
              <h1 class="text-2xl font-bold text-gray-900">{{ student.name }}</h1>
              <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 uppercase">
                {{ student.status }}
              </span>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">
              Class: {{ student.class_group?.name || 'Not assigned' }} &bull; Enrolled: {{ formatDate(student.enrollment_date) }}
            </p>
          </div>
        </div>

        <div class="flex space-x-2">
          <Link
            :href="route('students.edit', student.id)"
            class="inline-flex items-center px-3.5 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            <PencilIcon class="w-4 h-4 mr-1.5" />
            Edit Profile
          </Link>
          <button
            @click="deleteStudent"
            class="inline-flex items-center px-3.5 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-600 bg-white hover:bg-red-50"
          >
            <TrashIcon class="w-4 h-4 mr-1.5" />
            Delete
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Particulars Card -->
        <div class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-4">
          <h2 class="text-base font-semibold text-gray-900 border-b pb-2">Student Particulars</h2>
          <dl class="space-y-3 text-sm">
            <div>
              <dt class="text-xs font-medium text-gray-500 uppercase">Date of Birth</dt>
              <dd class="text-gray-900 font-medium mt-0.5">{{ formatDate(student.date_of_birth) }}</dd>
            </div>
            <div>
              <dt class="text-xs font-medium text-gray-500 uppercase">Gender</dt>
              <dd class="text-gray-900 font-medium capitalize mt-0.5">{{ student.gender }}</dd>
            </div>
            <div>
              <dt class="text-xs font-medium text-gray-500 uppercase">Allergies / Dietary</dt>
              <dd class="text-gray-900 font-medium mt-0.5">{{ student.allergies || 'None specified' }}</dd>
            </div>
            <div>
              <dt class="text-xs font-medium text-gray-500 uppercase">Medical Notes</dt>
              <dd class="text-gray-900 font-medium mt-0.5">{{ student.medical_notes || 'None specified' }}</dd>
            </div>
          </dl>

          <h2 class="text-base font-semibold text-gray-900 border-b pt-4 pb-2">Guardian Contact</h2>
          <div v-for="g in student.guardians" :key="g.id" class="text-sm space-y-1">
            <div class="font-bold text-gray-900">{{ g.name }} <span class="font-normal text-xs text-gray-500">({{ g.pivot?.relationship || g.relationship }})</span></div>
            <div class="text-gray-600">Phone: {{ g.phone }}</div>
            <div v-if="g.email" class="text-gray-600">Email: {{ g.email }}</div>
            <div v-if="g.address" class="text-gray-600 text-xs mt-1">{{ g.address }}</div>
          </div>
        </div>

        <!-- Assigned Fees & Invoices (2 Columns) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Assigned Fees -->
          <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-base font-semibold text-gray-900">Assigned Fee Templates</h2>
              <Link :href="route('fees.assign', { search: student.name })" class="text-xs text-indigo-600 font-medium hover:underline">
                Manage Fee Assignments &rarr;
              </Link>
            </div>

            <div class="divide-y divide-gray-100">
              <div v-for="sf in student.student_fees" :key="sf.id" class="py-2.5 flex items-center justify-between text-sm">
                <div>
                  <div class="font-medium text-gray-800">{{ sf.fee_template?.name }}</div>
                  <div class="text-xs text-gray-400">
                    Effective: {{ formatDate(sf.effective_from) }}
                    <span v-if="sf.discount_amount > 0" class="text-emerald-600 ml-1">(- {{ formatCurrency(sf.discount_amount) }} {{ sf.discount_reason }})</span>
                  </div>
                </div>
                <div class="font-bold text-gray-900">
                  {{ formatCurrency(sf.custom_amount ?? sf.fee_template?.amount) }}
                </div>
              </div>

              <div v-if="!student.student_fees || student.student_fees.length === 0" class="py-4 text-center text-sm text-gray-400">
                No active fees assigned yet.
              </div>
            </div>
          </div>

          <!-- Billing History Invoices -->
          <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-base font-semibold text-gray-900">Recent Invoices</h2>
              <Link :href="route('invoices.create', { student_id: student.id })" class="text-xs text-indigo-600 font-medium hover:underline">
                + Create Invoice
              </Link>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                  <tr class="text-gray-500 text-xs uppercase">
                    <th class="text-left py-2">Invoice #</th>
                    <th class="text-left py-2">Month</th>
                    <th class="text-right py-2">Total</th>
                    <th class="text-right py-2">Balance</th>
                    <th class="text-center py-2">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="inv in student.invoices" :key="inv.id">
                    <td class="py-2.5 font-medium text-indigo-600">
                      <Link :href="route('invoices.show', inv.id)">{{ inv.invoice_number }}</Link>
                    </td>
                    <td class="py-2.5 text-gray-600">{{ formatDate(inv.billing_month) }}</td>
                    <td class="py-2.5 text-right font-medium text-gray-900">{{ formatCurrency(inv.total_amount) }}</td>
                    <td class="py-2.5 text-right font-bold" :class="inv.balance_due > 0 ? 'text-rose-600' : 'text-emerald-600'">
                      {{ formatCurrency(inv.balance_due) }}
                    </td>
                    <td class="py-2.5 text-center">
                      <span class="px-2 py-0.5 text-xs font-semibold rounded-full uppercase" :class="inv.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                        {{ inv.status }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="!student.invoices || student.invoices.length === 0">
                    <td colspan="5" class="py-4 text-center text-sm text-gray-400">
                      No invoices issued for this student yet.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
