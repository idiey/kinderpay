<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const quickLogin = (email, password = 'password') => {
    form.email = email;
    form.password = password;
    form.remember = true;
    submit();
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <!-- 1-Click Quick Demo Login Card -->
        <div class="mb-6 p-4 rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 to-purple-50/50 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-indigo-700 flex items-center gap-1.5">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    1-Click Demo Login
                </span>
                <span class="text-[10px] text-gray-400">Click role to auto-login</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <button
                    type="button"
                    @click="quickLogin('demo@kinderpay.test')"
                    :disabled="form.processing"
                    class="group relative flex flex-col items-start p-2.5 rounded-lg border border-indigo-200 bg-white hover:border-indigo-500 hover:shadow-md transition-all text-left disabled:opacity-50"
                >
                    <div class="flex items-center gap-1.5 w-full">
                        <span class="text-base">🏫</span>
                        <span class="text-xs font-bold text-gray-900 group-hover:text-indigo-600">Principal</span>
                    </div>
                    <span class="text-[10px] text-gray-500 truncate w-full mt-0.5">Admin & Billing</span>
                    <span class="mt-1 text-[9px] font-mono text-indigo-500 bg-indigo-50 px-1 py-0.5 rounded">demo@kinderpay.test</span>
                </button>

                <button
                    type="button"
                    @click="quickLogin('parent@kinderpay.test')"
                    :disabled="form.processing"
                    class="group relative flex flex-col items-start p-2.5 rounded-lg border border-purple-200 bg-white hover:border-purple-500 hover:shadow-md transition-all text-left disabled:opacity-50"
                >
                    <div class="flex items-center gap-1.5 w-full">
                        <span class="text-base">👨‍👩‍👧</span>
                        <span class="text-xs font-bold text-gray-900 group-hover:text-purple-600">Parent</span>
                    </div>
                    <span class="text-[10px] text-gray-500 truncate w-full mt-0.5">Portal & Invoices</span>
                    <span class="mt-1 text-[9px] font-mono text-purple-500 bg-purple-50 px-1 py-0.5 rounded">parent@kinderpay.test</span>
                </button>

                <button
                    type="button"
                    @click="quickLogin('admin@kinderpay.test')"
                    :disabled="form.processing"
                    class="group relative flex flex-col items-start p-2.5 rounded-lg border border-slate-200 bg-white hover:border-slate-500 hover:shadow-md transition-all text-left disabled:opacity-50"
                >
                    <div class="flex items-center gap-1.5 w-full">
                        <span class="text-base">⚡</span>
                        <span class="text-xs font-bold text-gray-900 group-hover:text-slate-800">Super Admin</span>
                    </div>
                    <span class="text-[10px] text-gray-500 truncate w-full mt-0.5">Platform Ops</span>
                    <span class="mt-1 text-[9px] font-mono text-slate-600 bg-slate-100 px-1 py-0.5 rounded">admin@kinderpay.test</span>
                </button>
            </div>
        </div>

        <div class="relative flex py-1 items-center mb-4">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="flex-shrink mx-3 text-xs text-gray-400 font-medium">or login manually</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
