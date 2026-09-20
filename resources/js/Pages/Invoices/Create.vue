<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  students: Array,
  templates: Array,
});

const form = useForm({
  student_id: '',
  billing_month: new Date().toISOString().substring(0, 7) + '-01',
  due_date: new Date(Date.now() + 14 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  notes: '',
  items: [
    {
      description: 'Tuition Fee',
      fee_template_id: '',
      quantity: 1,
      unit_price: 350.00,
      discount: 0.00,
    }
  ]
});

const addItem = () => {
  form.items.push({
    description: '',
    fee_template_id: '',
    quantity: 1,
    unit_price: 0,
    discount: 0,
  });
};

const removeItem = (idx) => {
  if (form.items.length > 1) {
    form.items.splice(idx, 1);
  }
};

const onTemplateSelect = (idx, templateId) => {
  const tpl = props.templates.find(t => t.id === templateId);
  if (tpl) {
    form.items[idx].description = tpl.name;
    form.items[idx].unit_price = Number(tpl.amount);
  }
};

const calculateTotal = () => {
  return form.items.reduce((acc, item) => {
    const total = ((item.quantity || 1) * (item.unit_price || 0)) - (item.discount || 0);
    return acc + Math.max(0, total);
  }, 0);
};

const submit = () => {
  form.post(route('invoices.store'));
};
</script>

<template>
  <Head title="Create Invoice" />

  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center space-x-4">
        <Link :href="route('invoices.index')" class="text-gray-500 hover:text-gray-700">
          <ArrowLeftIcon class="w-5 h-5" />
        </Link>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Create Ad-hoc Invoice</h1>
          <p class="text-sm text-gray-500 mt-0.5">Generate a one-time bill for materials, field trip, uniform, or custom fee</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-6 border border-gray-100 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="sm:col-span-3">
            <label class="block text-xs font-semibold text-gray-700 uppercase">Select Student *</label>
            <select
              v-model="form.student_id"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="">-- Choose Student --</option>
              <option v-for="s in students" :key="s.id" :value="s.id">
                {{ s.name }} ({{ s.class_group?.name || 'No Class' }})
              </option>
            </select>
            <span v-if="form.errors.student_id" class="text-xs text-red-600 mt-1">{{ form.errors.student_id }}</span>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Billing Month *</label>
            <input
              v-model="form.billing_month"
              type="date"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Payment Due Date *</label>
            <input
              v-model="form.due_date"
              type="date"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Notes / Remarks</label>
            <input
              v-model="form.notes"
              type="text"
              placeholder="e.g. Sports Day T-shirt"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>
        </div>

        <!-- Line Items Section -->
        <div>
          <div class="flex items-center justify-between border-b pb-2 mb-3">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Invoice Line Items</h2>
            <button
              type="button"
              @click="addItem"
              class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800"
            >
              <PlusIcon class="w-3.5 h-3.5 mr-1" />
              Add Item
            </button>
          </div>

          <div class="space-y-3">
            <div
              v-for="(item, idx) in form.items"
              :key="idx"
              class="p-3 bg-gray-50 rounded-lg border border-gray-200 grid grid-cols-12 gap-3 items-center"
            >
              <div class="col-span-12 sm:col-span-4">
                <label class="block text-xs text-gray-500">Preset / Description</label>
                <div class="flex space-x-1 mt-1">
                  <select
                    v-model="item.fee_template_id"
                    @change="onTemplateSelect(idx, item.fee_template_id)"
                    class="w-1/3 rounded-md border-gray-300 text-xs py-1.5 focus:border-indigo-500"
                  >
                    <option value="">Custom</option>
                    <option v-for="t in templates" :key="t.id" :value="t.id">{{ t.name }}</option>
                  </select>
                  <input
                    v-model="item.description"
                    type="text"
                    required
                    placeholder="Item description"
                    class="w-2/3 rounded-md border-gray-300 text-xs py-1.5 focus:border-indigo-500"
                  />
                </div>
              </div>

              <div class="col-span-4 sm:col-span-2">
                <label class="block text-xs text-gray-500">Qty</label>
                <input
                  v-model.number="item.quantity"
                  type="number"
                  min="1"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 text-xs py-1.5 focus:border-indigo-500"
                />
              </div>

              <div class="col-span-4 sm:col-span-3">
                <label class="block text-xs text-gray-500">Unit Price (RM)</label>
                <input
                  v-model.number="item.unit_price"
                  type="number"
                  step="0.01"
                  min="0"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 text-xs py-1.5 focus:border-indigo-500"
                />
              </div>

              <div class="col-span-3 sm:col-span-2">
                <label class="block text-xs text-gray-500">Discount</label>
                <input
                  v-model.number="item.discount"
                  type="number"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full rounded-md border-gray-300 text-xs py-1.5 focus:border-indigo-500"
                />
              </div>

              <div class="col-span-1 flex justify-end pt-5">
                <button
                  type="button"
                  @click="removeItem(idx)"
                  class="text-gray-400 hover:text-red-600 transition"
                  title="Remove row"
                >
                  <TrashIcon class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>

          <!-- Total Calculation Display -->
          <div class="mt-4 flex justify-end">
            <div class="bg-gray-50 p-4 rounded-lg border text-right space-y-1 w-64">
              <div class="text-xs text-gray-500">Calculated Grand Total:</div>
              <div class="text-2xl font-bold text-indigo-600">
                RM {{ calculateTotal().toFixed(2) }}
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
          <Link
            :href="route('invoices.index')"
            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
          >
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 shadow-sm"
          >
            Create & Save Invoice
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
