<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
  kindergarten: Object,
});

const form = useForm({
  name: props.kindergarten?.name || '',
  registration_no: props.kindergarten?.registration_no || '',
  phone: props.kindergarten?.phone || '',
  email: props.kindergarten?.email || '',
  address: props.kindergarten?.address || '',
  city: props.kindergarten?.city || '',
  state: props.kindergarten?.state || '',
  postcode: props.kindergarten?.postcode || '',
  invoice_prefix: props.kindergarten?.invoice_prefix || 'INV',
  payment_gateway: props.kindergarten?.payment_gateway || 'billplz',
  gateway_api_key: props.kindergarten?.gateway_api_key || '',
  gateway_collection_id: props.kindergarten?.gateway_collection_id || '',
});

const submit = () => {
  form.put(route('settings.update'));
};
</script>

<template>
  <Head title="Kindergarten Settings" />

  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto space-y-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Kindergarten Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Configure school profile, billing prefix, and Billplz online payment gateway</p>
      </div>

      <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-6">
        <h2 class="text-base font-bold text-gray-900 border-b pb-2">School Profile</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 uppercase">Kindergarten Name *</label>
            <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">SSM / JPNIN Registration No.</label>
            <input v-model="form.registration_no" type="text" class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Invoice Number Prefix *</label>
            <input v-model="form.invoice_prefix" type="text" required class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Phone Number</label>
            <input v-model="form.phone" type="tel" class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Official Email</label>
            <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 uppercase">Premises Address</label>
            <textarea v-model="form.address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></textarea>
          </div>
        </div>

        <h2 class="text-base font-bold text-gray-900 border-b pt-4 pb-2">Online Payment Gateway (Billplz FPX)</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Gateway Provider</label>
            <select v-model="form.payment_gateway" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
              <option value="billplz">Billplz (Malaysia FPX)</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Billplz Collection ID</label>
            <input v-model="form.gateway_collection_id" type="text" placeholder="e.g. in_12345" class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 uppercase">Billplz API Secret Key</label>
            <input v-model="form.gateway_api_key" type="password" placeholder="Will be securely encrypted at rest" class="mt-1 block w-full rounded-md border-gray-300 text-sm" />
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="form.processing" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 shadow-sm">
            Save Settings
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
