<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  students: Object,
  classes: Array,
  templates: Array,
  filters: Object,
});

const isModalOpen = ref(false);
const assignMode = ref('class'); // 'class' or 'student'

const form = useForm({
  class_group_id: '',
  student_id: '',
  fee_template_id: props.templates?.[0]?.id || '',
  custom_amount: '',
  discount_amount: 0,
  discount_reason: '',
  effective_from: new Date().toISOString().split('T')[0],
  effective_to: '',
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(val || 0);
};

const openAssignModal = (studentId = null) => {
  form.reset();
  form.effective_from = new Date().toISOString().split('T')[0];
  form.fee_template_id = props.templates?.[0]?.id || '';
  if (studentId) {
    assignMode.value = 'student';
    form.student_id = studentId;
  } else {
    assignMode.value = 'class';
  }
  isModalOpen.value = true;
};

const submit = () => {
  form.post(route('fees.assign.store'), {
    onSuccess: () => {
      isModalOpen.value = false;
    }
  });
};

const removeFee = (feeId) => {
  if (confirm('Remove this assigned fee template from the student?')) {
    useForm({}).delete(route('fees.assign.destroy', feeId));
  }
};
</script>

<template>
  <Head title="Assign Fees" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Fee Allocation</h1>
          <p class="text-sm text-gray-500 mt-1">Assign recurring fee templates to students or whole classes</p>
        </div>
        <button
          @click="openAssignModal()"
          class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <PlusIcon class="w-4 h-4 mr-1.5" />
          Assign Fee Template
        </button>
      </div>

      <!-- Assign Fee Modal -->
      <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 space-y-4">
          <h3 class="text-lg font-bold text-gray-900">Assign Fee Template</h3>

          <!-- Mode Toggle -->
          <div class="flex rounded-md shadow-sm">
            <button
              type="button"
              @click="assignMode = 'class'; form.student_id = ''"
              :class="[assignMode === 'class' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50', 'w-1/2 py-2 text-xs font-semibold rounded-l-md border border-gray-300 uppercase']"
            >
              Assign to Entire Class
            </button>
            <button
              type="button"
              @click="assignMode = 'student'; form.class_group_id = ''"
              :class="[assignMode === 'student' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50', 'w-1/2 py-2 text-xs font-semibold rounded-r-md border border-l-0 border-gray-300 uppercase']"
            >
              Assign to Single Student
            </button>
          </div>

          <form @submit.prevent="submit" class="space-y-4">
            <div v-if="assignMode === 'class'">
              <label class="block text-xs font-semibold text-gray-700 uppercase">Select Class *</label>
              <select
                v-model="form.class_group_id"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">-- Choose Class --</option>
                <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>

            <div v-else>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Select Student *</label>
              <select
                v-model="form.student_id"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">-- Choose Student --</option>
                <option v-for="s in students.data" :key="s.id" :value="s.id">{{ s.name }} ({{ s.class_group?.name || 'No Class' }})</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Fee Template *</label>
              <select
                v-model="form.fee_template_id"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option v-for="t in templates" :key="t.id" :value="t.id">
                  {{ t.name }} - RM {{ t.amount }} ({{ t.frequency }})
                </option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Custom Rate (Optional)</label>
                <input
                  v-model="form.custom_amount"
                  type="number"
                  step="0.01"
                  placeholder="Overrides standard rate"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Discount (RM)</label>
                <input
                  v-model="form.discount_amount"
                  type="number"
                  step="0.01"
                  placeholder="0.00"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Discount Reason</label>
              <input
                v-model="form.discount_reason"
                type="text"
                placeholder="e.g. Sibling discount 10%"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Effective From *</label>
                <input
                  v-model="form.effective_from"
                  type="date"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Effective To</label>
                <input
                  v-model="form.effective_to"
                  type="date"
                  placeholder="Leave empty if ongoing"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
            </div>

            <div class="flex justify-end space-x-3 pt-3 border-t">
              <button
                type="button"
                @click="isModalOpen = false"
                class="px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md"
              >
                Save Assignment
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Students and their assigned fees table -->
      <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Student Name</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Class</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Active Assigned Fees</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white">
            <tr v-for="s in students.data" :key="s.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">{{ s.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ s.class_group?.name || 'Unassigned' }}
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="sf in s.student_fees"
                    :key="sf.id"
                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100"
                  >
                    {{ sf.fee_template?.name }}: {{ formatCurrency(sf.custom_amount ?? sf.fee_template?.amount) }}
                    <button
                      @click="removeFee(sf.id)"
                      class="ml-1.5 text-indigo-400 hover:text-red-600"
                      title="Remove fee"
                    >
                      &times;
                    </button>
                  </span>
                  <span v-if="!s.student_fees || s.student_fees.length === 0" class="text-xs text-gray-400 italic">
                    No fees assigned
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="openAssignModal(s.id)"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  + Assign Fee
                </button>
              </td>
            </tr>
            <tr v-if="students.data.length === 0">
              <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">
                No students enrolled yet.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
