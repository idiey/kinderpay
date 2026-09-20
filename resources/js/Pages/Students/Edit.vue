<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  student: Object,
  classes: Array,
});

const form = useForm({
  name: props.student.name,
  ic_number: props.student.ic_number || '',
  date_of_birth: props.student.date_of_birth ? props.student.date_of_birth.split('T')[0] : '',
  gender: props.student.gender,
  class_group_id: props.student.class_group_id || '',
  enrollment_date: props.student.enrollment_date ? props.student.enrollment_date.split('T')[0] : '',
  status: props.student.status,
  allergies: props.student.allergies || '',
  medical_notes: props.student.medical_notes || '',
});

const submit = () => {
  form.put(route('students.update', props.student.id));
};
</script>

<template>
  <Head :title="'Edit ' + student.name" />

  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center space-x-4">
        <Link :href="route('students.show', student.id)" class="text-gray-500 hover:text-gray-700">
          <ArrowLeftIcon class="w-5 h-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Edit Student Particulars</h1>
          <p class="text-sm text-gray-500 mt-0.5">{{ student.name }}</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 uppercase">Child Full Name *</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">MyKid / Birth Cert No.</label>
            <input
              v-model="form.ic_number"
              type="text"
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
            <label class="block text-xs font-semibold text-gray-700 uppercase">Status *</label>
            <select
              v-model="form.status"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="active">Active</option>
              <option value="graduated">Graduated</option>
              <option value="withdrawn">Withdrawn</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Class Allocation</label>
            <select
              v-model="form.class_group_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="">-- Unassigned --</option>
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
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 uppercase">Medical Notes</label>
            <textarea
              v-model="form.medical_notes"
              rows="2"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            ></textarea>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
          <Link
            :href="route('students.show', student.id)"
            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 shadow-sm"
          >
            Update Student
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
