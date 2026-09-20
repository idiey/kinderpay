<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const form = useForm({
  name: '',
  employee_id: '',
  ic_number: '',
  date_of_birth: '',
  gender: 'female',
  phone: '',
  email: '',
  position: 'Teacher',
  employment_type: 'full_time',
  join_date: new Date().toISOString().split('T')[0],
  basic_salary: 2000.00,
  bank_name: 'Maybank',
  bank_account: '',
  epf_number: '',
  socso_number: '',
  epf_category: 1,
  address: '',
});

const submit = () => {
  form.post(route('staff.store'));
};
</script>

<template>
  <Head title="Add Staff Member" />

  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center space-x-4">
        <Link :href="route('staff.index')" class="text-gray-500 hover:text-gray-700">
          <ArrowLeftIcon class="w-5 h-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Add Staff Member</h1>
          <p class="text-sm text-gray-500 mt-0.5">Register kindergarten teacher or employee particulars</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-4">
          <h2 class="text-base font-bold text-gray-900 border-b pb-2">Personal Details</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 uppercase">Full Name *</label>
              <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">IC / Passport Number</label>
              <input v-model="form.ic_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Employee Code / ID</label>
              <input v-model="form.employee_id" type="text" placeholder="e.g. STF-001" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Date of Birth</label>
              <input v-model="form.date_of_birth" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Gender *</label>
              <select v-model="form.gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                <option value="female">Female</option>
                <option value="male">Male</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Phone (WhatsApp) *</label>
              <input v-model="form.phone" type="tel" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Email Address</label>
              <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 uppercase">Residential Address</label>
              <textarea v-model="form.address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm"></textarea>
            </div>
          </div>

          <h2 class="text-base font-bold text-gray-900 border-b pt-4 pb-2">Employment & Compensation</h2>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Position / Role *</label>
              <input v-model="form.position" type="text" required placeholder="e.g. Senior Teacher" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Employment Type *</label>
              <select v-model="form.employment_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                <option value="full_time">Full Time</option>
                <option value="part_time">Part Time</option>
                <option value="contract">Contract</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Join Date *</label>
              <input v-model="form.join_date" type="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Basic Salary (RM) *</label>
              <input v-model="form.basic_salary" type="number" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm font-bold" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Bank Name</label>
              <input v-model="form.bank_name" type="text" placeholder="Maybank, CIMB, etc." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Bank Account Number</label>
              <input v-model="form.bank_account" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">EPF (KWSP) No.</label>
              <input v-model="form.epf_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">SOCSO (PERKESO) No.</label>
              <input v-model="form.socso_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">EPF Category</label>
              <select v-model="form.epf_category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                <option :value="1">Category 1 (Below 60 yrs - 11% / 13%)</option>
                <option :value="2">Category 2 (60+ yrs - 0% / 4%)</option>
              </select>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3">
          <Link :href="route('staff.index')" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md">Cancel</Link>
          <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-md hover:bg-indigo-700 shadow-sm">
            Save Staff Member
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
