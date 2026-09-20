<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { AcademicCapIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  classes: {
    type: Array,
    default: () => []
  }
});

const form = useForm({
  name: '',
  academic_year: new Date().getFullYear(),
  capacity: 25,
});

const isCreating = ref(false);

const submit = () => {
  form.post(route('classes.store'), {
    onSuccess: () => {
      form.reset();
      isCreating.value = false;
    }
  });
};

const deleteClass = (id) => {
  if (confirm('Are you sure you want to delete this class?')) {
    useForm({}).delete(route('classes.destroy', id));
  }
};
</script>

<template>
  <Head title="Classes Management" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Classes</h1>
          <p class="text-sm text-gray-500 mt-1">Organize student age groups and class cohorts</p>
        </div>
        <button
          @click="isCreating = !isCreating"
          class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <PlusIcon class="w-4 h-4 mr-1.5" />
          {{ isCreating ? 'Cancel' : 'Add Class' }}
        </button>
      </div>

      <!-- Add Class Inline Card -->
      <div v-if="isCreating" class="bg-white p-6 rounded-lg shadow border border-indigo-100">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Class</h3>
        <form @submit.prevent="submit" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Class Name</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="e.g. 5 Tahun Amanah"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Academic Year</label>
            <input
              v-model="form.academic_year"
              type="number"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Max Capacity</label>
            <input
              v-model="form.capacity"
              type="number"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>
          <div class="sm:col-span-3 flex justify-end space-x-3 mt-2">
            <button
              type="button"
              @click="isCreating = false"
              class="px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md"
            >
              Save Class
            </button>
          </div>
        </form>
      </div>

      <!-- Classes Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="c in classes"
          :key="c.id"
          class="bg-white rounded-lg shadow border border-gray-100 p-5 flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                Year {{ c.academic_year }}
              </span>
              <button
                @click="deleteClass(c.id)"
                class="text-gray-400 hover:text-red-600 transition"
                title="Delete class"
              >
                <TrashIcon class="w-4 h-4" />
              </button>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mt-2">{{ c.name }}</h3>
            <p class="text-sm text-gray-500 mt-1">Capacity: {{ c.capacity || 'Unlimited' }} students</p>
          </div>

          <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-sm">
            <div class="flex items-center text-gray-600">
              <AcademicCapIcon class="w-5 h-5 mr-1.5 text-indigo-500" />
              <span class="font-semibold">{{ c.students_count || 0 }}</span>
              <span class="ml-1 text-gray-400">enrolled</span>
            </div>
          </div>
        </div>

        <div v-if="classes.length === 0" class="col-span-full py-12 text-center text-gray-400 bg-white rounded-lg border border-dashed border-gray-300">
          No classes defined yet. Click "Add Class" to create your first class.
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
