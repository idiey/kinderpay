<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { PlusIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  requests: Object,
  leaveTypes: Array,
  staffList: Array,
  holidays: Array,
  filters: Object,
});

const showModal = ref(false);
const form = useForm({
  staff_id: props.staffList?.[0]?.id || '',
  leave_type_id: props.leaveTypes?.[0]?.id || '',
  start_date: new Date().toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0],
  is_half_day: false,
  reason: '',
});

const submit = () => {
  form.post(route('leave.store'), {
    onSuccess: () => {
      showModal.value = false;
      form.reset();
    }
  });
};

const reviewLeave = (id, action) => {
  const note = prompt(`Enter review note for ${action}:`) || '';
  useForm({
    action: action,
    review_notes: note,
  }).post(route('leave.review', id));
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric' });
};

const getStatusBadge = (st) => {
  switch (st) {
    case 'approved': return 'bg-green-100 text-green-800';
    case 'rejected': return 'bg-red-100 text-red-800';
    default: return 'bg-yellow-100 text-yellow-800';
  }
};
</script>

<template>
  <Head title="Staff Leave Management" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Leave Management</h1>
          <p class="text-sm text-gray-500 mt-1">Staff leave applications, balance tracking, and approval workflow</p>
        </div>
        <button
          @click="showModal = true"
          class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <PlusIcon class="w-4 h-4 mr-1.5" />
          Apply for Leave
        </button>
      </div>

      <!-- Leave Requests Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Staff Member</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Leave Type</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dates</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Days</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reason</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="r in requests.data" :key="r.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">{{ r.staff?.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                    {{ r.leave_type?.name }} ({{ r.leave_type?.code }})
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  {{ formatDate(r.start_date) }} &rarr; {{ formatDate(r.end_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-indigo-600">
                  {{ r.days }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ r.reason || '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span :class="['px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase', getStatusBadge(r.status)]">
                    {{ r.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <template v-if="r.status === 'pending'">
                    <button @click="reviewLeave(r.id, 'approve')" class="text-green-600 hover:text-green-900 font-bold mr-2">Approve</button>
                    <button @click="reviewLeave(r.id, 'reject')" class="text-red-600 hover:text-red-900 font-bold">Reject</button>
                  </template>
                  <span v-else class="text-xs text-gray-400">Reviewed</span>
                </td>
              </tr>
              <tr v-if="requests.data.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">No leave requests found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Apply Leave Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 space-y-4">
          <h3 class="text-lg font-bold text-gray-900">Apply for Staff Leave</h3>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Staff Member *</label>
              <select v-model="form.staff_id" required class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                <option v-for="s in staffList" :key="s.id" :value="s.id">{{ s.name }} ({{ s.position }})</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Leave Category *</label>
              <select v-model="form.leave_type_id" required class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                <option v-for="lt in leaveTypes" :key="lt.id" :value="lt.id">{{ lt.name }} ({{ lt.code }}) - {{ lt.is_paid ? 'Paid' : 'Unpaid' }}</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Start Date *</label>
                <input v-model="form.start_date" type="date" required class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">End Date *</label>
                <input v-model="form.end_date" type="date" required class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Reason / Justification</label>
              <textarea v-model="form.reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm" placeholder="e.g. Medical appointment"></textarea>
            </div>
            <div class="flex justify-end space-x-3 pt-3 border-t">
              <button type="button" @click="showModal = false" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md">Cancel</button>
              <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-md">Submit Application</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
