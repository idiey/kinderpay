<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  BuildingLibraryIcon,
  PlusIcon,
  UserGroupIcon,
  AcademicCapIcon,
  DocumentTextIcon,
  CheckCircleIcon,
  MapPinIcon,
  PhoneIcon,
  EnvelopeIcon,
  ArrowRightCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  kindergartens: Array,
  activeKindergartenId: Number,
  isSuperAdmin: Boolean,
});

const showModal = ref(false);

const form = useForm({
  name: '',
  registration_no: '',
  invoice_prefix: '',
  invoice_day: 1,
  phone: '',
  email: '',
  address: '',
  city: '',
  state: '',
  postcode: '',
  payment_gateway: 'billplz',
});

const submit = () => {
  form.post(route('kindergartens.store'), {
    onSuccess: () => {
      showModal.value = false;
      form.reset();
    },
  });
};

const switchBranch = (id) => {
  router.post(route('tenants.switch'), { kindergarten_id: id });
};
</script>

<template>
  <Head title="Kindergarten Branches" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="sm:flex sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Kindergarten Branches</h1>
          <p class="mt-1 text-sm text-gray-500">
            Manage your multi-branch kindergarten network, registration numbers, and operational configurations.
          </p>
        </div>
        <div v-if="isSuperAdmin" class="mt-4 sm:mt-0">
          <button
            type="button"
            @click="showModal = true"
            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600"
          >
            <PlusIcon class="w-5 h-5" />
            Register New Branch
          </button>
        </div>
      </div>

      <!-- Branch Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="branch in kindergartens"
          :key="branch.id"
          class="relative rounded-2xl border transition-all duration-200 p-6 flex flex-col justify-between bg-white shadow-sm"
          :class="branch.id === activeKindergartenId ? 'border-indigo-500 ring-2 ring-indigo-500/20 shadow-md' : 'border-gray-200 hover:border-gray-300 hover:shadow'"
        >
          <div>
            <!-- Active Badge & Prefix -->
            <div class="flex items-center justify-between mb-3">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider bg-slate-100 text-slate-700 font-mono">
                Prefix: {{ branch.invoice_prefix }}
              </span>
              <span
                v-if="branch.id === activeKindergartenId"
                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"
              >
                <CheckCircleIcon class="w-3.5 h-3.5 text-emerald-600" />
                Active Branch
              </span>
            </div>

            <!-- Branch Name & SSM -->
            <h2 class="text-lg font-bold text-gray-900">{{ branch.name }}</h2>
            <p v-if="branch.registration_no" class="text-xs text-gray-400 font-mono mt-0.5">
              SSM: {{ branch.registration_no }}
            </p>

            <!-- Address & Contact -->
            <div class="mt-4 space-y-1.5 text-xs text-gray-600">
              <div v-if="branch.city || branch.state" class="flex items-center gap-2">
                <MapPinIcon class="w-4 h-4 text-gray-400 flex-shrink-0" />
                <span class="truncate">{{ branch.city }}, {{ branch.state }}</span>
              </div>
              <div v-if="branch.phone" class="flex items-center gap-2">
                <PhoneIcon class="w-4 h-4 text-gray-400 flex-shrink-0" />
                <span>{{ branch.phone }}</span>
              </div>
              <div v-if="branch.email" class="flex items-center gap-2">
                <EnvelopeIcon class="w-4 h-4 text-gray-400 flex-shrink-0" />
                <span class="truncate">{{ branch.email }}</span>
              </div>
            </div>

            <!-- Branch Metrics -->
            <div class="mt-6 pt-4 border-t border-gray-100 grid grid-cols-3 gap-2 text-center">
              <div class="p-2 rounded-lg bg-gray-50">
                <div class="text-xs text-gray-400">Students</div>
                <div class="text-base font-bold text-gray-900">{{ branch.students_count || 0 }}</div>
              </div>
              <div class="p-2 rounded-lg bg-gray-50">
                <div class="text-xs text-gray-400">Staff</div>
                <div class="text-base font-bold text-gray-900">{{ branch.staff_count || 0 }}</div>
              </div>
              <div class="p-2 rounded-lg bg-gray-50">
                <div class="text-xs text-gray-400">Classes</div>
                <div class="text-base font-bold text-gray-900">{{ branch.class_groups_count || 0 }}</div>
              </div>
            </div>
          </div>

          <!-- Switch or Selected Button -->
          <div class="mt-6 pt-4 border-t border-gray-100">
            <button
              v-if="branch.id !== activeKindergartenId && isSuperAdmin"
              type="button"
              @click="switchBranch(branch.id)"
              class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition"
            >
              <ArrowRightCircleIcon class="w-4 h-4" />
              Switch to this Branch
            </button>
            <div
              v-else-if="branch.id === activeKindergartenId"
              class="w-full text-center py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-lg"
            >
              Currently Working on this Branch
            </div>
          </div>
        </div>
      </div>

      <!-- Register New Branch Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl p-6 border border-gray-100">
          <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
              <BuildingLibraryIcon class="w-5 h-5 text-indigo-600" />
              Register New Kindergarten Branch
            </h3>
            <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
          </div>

          <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700">Branch / Kindergarten Name *</label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="e.g. Tadika Ceria Cawangan Bangi"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
              <span v-if="form.errors.name" class="text-xs text-red-600">{{ form.errors.name }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-700">SSM Registration No.</label>
                <input
                  v-model="form.registration_no"
                  type="text"
                  placeholder="e.g. SSM-2026-TDK0021"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700">Invoice Prefix (3-5 Chars) *</label>
                <input
                  v-model="form.invoice_prefix"
                  type="text"
                  required
                  placeholder="e.g. TCB"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm font-mono uppercase focus:border-indigo-500 focus:ring-indigo-500"
                />
                <span v-if="form.errors.invoice_prefix" class="text-xs text-red-600">{{ form.errors.invoice_prefix }}</span>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-700">Phone Number</label>
                <input
                  v-model="form.phone"
                  type="text"
                  placeholder="e.g. 03-89211234"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700">Email Address</label>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="e.g. bangi@ceriademo.edu.my"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-700">City</label>
                <input
                  v-model="form.city"
                  type="text"
                  placeholder="e.g. Bandar Baru Bangi"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700">State</label>
                <input
                  v-model="form.state"
                  type="text"
                  placeholder="e.g. Selangor"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700">Postcode</label>
                <input
                  v-model="form.postcode"
                  type="text"
                  placeholder="e.g. 43650"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
              <button
                type="button"
                @click="showModal = false"
                class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
              >
                Save & Initialize Branch
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
