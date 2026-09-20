<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  staff: Object,
});

const form = useForm({
  name: props.staff.name,
  employee_id: props.staff.employee_id || '',
  ic_number: props.staff.ic_number || '',
  date_of_birth: props.staff.date_of_birth ? props.staff.date_of_birth.split('T')[0] : '',
  gender: props.staff.gender,
  phone: props.staff.phone,
  email: props.staff.email || '',
  position: props.staff.position,
  employment_type: props.staff.employment_type,
  join_date: props.staff.join_date ? props.staff.join_date.split('T')[0] : '',
  end_date: props.staff.end_date ? props.staff.end_date.split('T')[0] : '',
  status: props.staff.status,
  basic_salary: props.staff.basic_salary,
  bank_name: props.staff.bank_name || '',
  bank_account: props.staff.bank_account || '',
  epf_number: props.staff.epf_number || '',
  socso_number: props.staff.socso_number || '',
  epf_category: props.staff.epf_category,
  address: props.staff.address || '',
});

const submit = () => {
  form.put(route('staff.update', props.staff.id));
};
</script>

<template>
  <Head :title="'Edit ' + staff.name" />

  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center space-x-4">
        <Link :href="route('staff.show', staff.id)" class="text-gray-500 hover:text-gray-700">
          <ArrowLeftIcon class="w-5 h-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Edit Staff Record</h1>
          <p class="text-sm text-gray-500 mt-0.5">{{ staff.name }}</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 uppercase">Full Name *</label>
            <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Position / Role *</label>
            <input v-model="form.position" type="text" required class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Status *</label>
            <select v-model="form.status" required class="mt-1 block w-full rounded-md border-gray-300 text-sm">
              <option value="active">Active</option>
              <option value="resigned">Resigned</option>
              <option value="terminated">Terminated</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Basic Monthly Salary (RM) *</label>
            <input v-model="form.basic_salary" type="number" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 text-sm font-bold" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Phone *</label>
            <input v-model="form.phone" type="tel" required class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Email</label>
            <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Bank Account</label>
            <input v-model="form.bank_account" type="text" class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
          <Link :href="route('staff.show', staff.id)" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md">Cancel</Link>
          <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-md hover:bg-indigo-700 shadow-sm">
            Update Staff Record
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
