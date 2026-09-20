<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  classes: Array,
});

const form = useForm({
  name: '',
  ic_number: '',
  date_of_birth: '',
  gender: 'male',
  class_group_id: '',
  enrollment_date: new Date().toISOString().split('T')[0],
  allergies: '',
  medical_notes: '',
  // Guardian
  guardian_name: '',
  guardian_relationship: 'mother',
  guardian_phone: '',
  guardian_email: '',
  guardian_address: '',
});

const submit = () => {
  form.post(route('students.store'));
};
</script>

<template>
  <Head title="Enroll Student" />

  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center space-x-4">
        <Link :href="route('students.index')" class="text-gray-500 hover:text-gray-700">
          <ArrowLeftIcon class="w-5 h-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Enroll New Student</h1>
          <p class="text-sm text-gray-500 mt-0.5">Register student particulars and guardian contact information</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Student Information Card -->
        <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Student Particulars</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 uppercase">Child Full Name *</label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="e.g. Adam bin Mohd Fauzi"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
              <span v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</span>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">MyKid / Birth Cert No.</label>
              <input
                v-model="form.ic_number"
                type="text"
                placeholder="e.g. 210515-10-1234"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Date of Birth *</label>
              <input
                v-model="form.date_of_birth"
                type="date"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Gender *</label>
              <select
                v-model="form.gender"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="male">Male / Lelaki</option>
                <option value="female">Female / Perempuan</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Class Allocation</label>
              <select
                v-model="form.class_group_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="">-- Assign Later --</option>
                <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }} (Year {{ c.academic_year }})</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Enrollment Date *</label>
              <input
                v-model="form.enrollment_date"
                type="date"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 uppercase">Allergies / Special Diet</label>
              <input
                v-model="form.allergies"
                type="text"
                placeholder="e.g. Peanuts allergy, lactose intolerant"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 uppercase">Medical Notes / Precautions</label>
              <textarea
                v-model="form.medical_notes"
                rows="2"
                placeholder="e.g. Asthmatic inhaler kept in bag"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Guardian Information Card -->
        <div class="bg-white shadow rounded-lg p-6 border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Primary Guardian Details</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 uppercase">Guardian Name *</label>
              <input
                v-model="form.guardian_name"
                type="text"
                required
                placeholder="e.g. Siti Nurhaliza binti Tarudin"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Relationship *</label>
              <select
                v-model="form.guardian_relationship"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="mother">Mother / Ibu</option>
                <option value="father">Father / Bapa</option>
                <option value="guardian">Guardian / Penjaga</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase">Phone (WhatsApp) *</label>
              <input
                v-model="form.guardian_phone"
                type="tel"
                required
                placeholder="e.g. 0123456789"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 uppercase">Email (For Invoices)</label>
              <input
                v-model="form.guardian_email"
                type="email"
                placeholder="e.g. parent@gmail.com"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-gray-700 uppercase">Residential Address</label>
              <textarea
                v-model="form.guardian_address"
                rows="2"
                placeholder="Full home address"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              ></textarea>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3">
          <Link
            :href="route('students.index')"
            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 shadow-sm"
          >
            Save & Enroll Student
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
