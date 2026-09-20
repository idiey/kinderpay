<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    SparklesIcon,
    BanknotesIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    ShieldCheckIcon,
    DocumentTextIcon,
    ArrowRightIcon,
    CreditCardIcon,
    DevicePhoneMobileIcon
} from '@heroicons/vue/24/outline';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const features = [
    {
        name: 'Automated Tuition Billing',
        desc: 'Generate monthly invoices in 1 click for tuition, meal plans, transportation, and custom materials. Flexible early-bird and sibling discounts.',
        icon: BanknotesIcon,
        color: 'from-blue-500 to-indigo-600',
    },
    {
        name: 'Billplz FPX & Manual Receipts',
        desc: 'Empower parents to pay instantly via Malaysian FPX direct online banking with real-time webhooks or easily record cash & CDM transfers.',
        icon: CreditCardIcon,
        color: 'from-emerald-500 to-teal-600',
    },
    {
        name: 'Malaysian Statutory Payroll',
        desc: 'Engineered for Malaysia: Automated EPF (KWSP 11%/13%/4%), SOCSO (PERKESO), and EIS (SIP) statutory salary calculations with payslip downloads.',
        icon: DocumentTextIcon,
        color: 'from-purple-500 to-indigo-600',
    },
    {
        name: 'Staff & Leave Management',
        desc: 'Calculate working days automatically while excluding weekends and Malaysian state public holidays with multi-level principal approvals.',
        icon: CalendarDaysIcon,
        color: 'from-amber-500 to-orange-600',
    },
    {
        name: 'Dedicated Parent Portal',
        desc: 'Give parents instant visibility into their enrolled children, itemized fee breakdowns, outstanding balances, and official PDF receipts.',
        icon: DevicePhoneMobileIcon,
        color: 'from-pink-500 to-rose-600',
    },
    {
        name: 'Multi-Tenant Security',
        desc: 'Rock-solid kindergarten data isolation, Spatie role-based access control (Super Admin, Principal, Accounts, Teacher, Parent) & audit logs.',
        icon: ShieldCheckIcon,
        color: 'from-cyan-500 to-blue-600',
    },
];

const demoAccounts = [
    {
        role: 'School Principal / Admin',
        email: 'demo@kinderpay.test',
        desc: 'Full access to student enrollment, batch billing, staff leave approvals, and payroll runs.',
        badge: 'Admin Access',
        color: 'bg-indigo-50 border-indigo-200 text-indigo-700',
    },
    {
        role: 'Parent Portal Demo',
        email: 'parent@kinderpay.test',
        desc: 'View enrolled child (Muhammad Rayyan), invoice statements, balance due & FPX pay button.',
        badge: 'Parent Access',
        color: 'bg-emerald-50 border-emerald-200 text-emerald-700',
    },
    {
        role: 'Super Administrator',
        email: 'admin@kinderpay.test',
        desc: 'Platform-level overview, kindergarten branch tenant configurations, and global rate tables.',
        badge: 'Super Admin',
        color: 'bg-amber-50 border-amber-200 text-amber-700',
    },
];
</script>

<template>
    <Head title="KinderPay — Modern Kindergarten Management, Billing & Statutory Payroll" />

    <div class="min-h-screen bg-slate-900 text-white selection:bg-indigo-500 selection:text-white">
        <!-- Background subtle glow elements -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-gradient-to-b from-indigo-600/20 via-purple-600/10 to-transparent blur-3xl pointer-events-none -z-10"></div>

        <!-- Navigation Bar -->
        <nav class="border-b border-slate-800/80 backdrop-blur-md sticky top-0 z-50 bg-slate-900/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/25">
                        <SparklesIcon class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-white via-slate-100 to-indigo-200 bg-clip-text text-transparent">KinderPay</span>
                        <span class="hidden sm:inline-block ml-2 text-xs uppercase px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 font-semibold border border-indigo-500/30">Malaysia SaaS</span>
                    </div>
                </div>

                <div class="flex items-center space-x-3 sm:space-x-4">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/30 transition flex items-center"
                    >
                        Go to Dashboard &rarr;
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white transition"
                        >
                            Sign In
                        </Link>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 text-sm font-semibold rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white shadow-lg shadow-indigo-500/20 transition flex items-center space-x-1.5"
                        >
                            <span>1-Click Demo Login</span>
                            <ArrowRightIcon class="w-4 h-4 ml-1" />
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="relative pt-16 pb-20 sm:pt-24 sm:pb-32 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-indigo-950/60 border border-indigo-500/30 text-indigo-300 text-xs font-medium mb-8 backdrop-blur shadow-inner">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Built for Malaysian Tadika, Taska & Preschools</span>
                </div>

                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight max-w-4xl mx-auto leading-tight sm:leading-none">
                    Kindergarten billing & payroll, <br />
                    <span class="bg-gradient-to-r from-indigo-400 via-purple-300 to-pink-400 bg-clip-text text-transparent">simplified on autopilot.</span>
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                    End tuition fee collection headaches. Issue automated invoices, collect payments via Billplz FPX, track teacher leaves, and run compliant Malaysian payroll in minutes.
                </p>

                <!-- CTA Buttons -->
                <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
                    <Link
                        :href="route('login')"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-base shadow-xl shadow-indigo-600/30 hover:scale-[1.02] active:scale-[0.98] transition flex items-center justify-center space-x-2"
                    >
                        <span>Launch Demo Environment</span>
                        <ArrowRightIcon class="w-5 h-5" />
                    </Link>
                    <a
                        href="#features"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white font-medium text-base border border-slate-700/80 transition"
                    >
                        Explore Feature Modules
                    </a>
                </div>

                <!-- Trust Metrics / Badges -->
                <div class="mt-16 pt-10 border-t border-slate-800/80 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto text-left">
                    <div class="bg-slate-800/40 p-4 rounded-xl border border-slate-800">
                        <div class="text-2xl font-bold text-white">100%</div>
                        <div class="text-xs text-slate-400 mt-0.5">KWSP, SOCSO, EIS Statutory Engine</div>
                    </div>
                    <div class="bg-slate-800/40 p-4 rounded-xl border border-slate-800">
                        <div class="text-2xl font-bold text-white">Instant FPX</div>
                        <div class="text-xs text-slate-400 mt-0.5">Online gateway with automated receipts</div>
                    </div>
                    <div class="bg-slate-800/40 p-4 rounded-xl border border-slate-800">
                        <div class="text-2xl font-bold text-white">Holiday-Aware</div>
                        <div class="text-xs text-slate-400 mt-0.5">Automated Malaysian state leave deductions</div>
                    </div>
                    <div class="bg-slate-800/40 p-4 rounded-xl border border-slate-800">
                        <div class="text-2xl font-bold text-white">Multi-Role</div>
                        <div class="text-xs text-slate-400 mt-0.5">Principal, Accounts, Teachers & Parents</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Quick 1-Click Role Accounts Preview -->
        <section class="py-16 bg-slate-950/60 border-y border-slate-800/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-white">Try It In 1-Click</h2>
                    <p class="text-slate-400 text-sm mt-2">
                        Pre-seeded demo credentials are ready on the login page. Pick any role to log in instantly:
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        v-for="acc in demoAccounts"
                        :key="acc.email"
                        class="bg-slate-900 rounded-2xl p-6 border border-slate-800 hover:border-indigo-500/50 transition relative flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full uppercase" :class="acc.color">
                                    {{ acc.badge }}
                                </span>
                                <span class="text-xs text-slate-400 font-mono">Password: password</span>
                            </div>
                            <h3 class="text-lg font-bold text-white">{{ acc.role }}</h3>
                            <p class="text-xs text-slate-400 mt-2 leading-relaxed">{{ acc.desc }}</p>
                            <div class="mt-4 p-2.5 rounded-lg bg-slate-950 border border-slate-800 text-xs font-mono text-indigo-300">
                                {{ acc.email }}
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-800/60">
                            <Link
                                :href="route('login')"
                                class="w-full block text-center py-2 px-4 rounded-lg bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white text-xs font-semibold transition"
                            >
                                Login with this account &rarr;
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Features Grid -->
        <section id="features" class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-xs uppercase tracking-widest text-indigo-400 font-bold">End-to-End Capabilities</h2>
                <p class="text-3xl sm:text-4xl font-extrabold text-white mt-2">Built to run your entire kindergarten</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div
                    v-for="f in features"
                    :key="f.name"
                    class="bg-slate-800/40 rounded-2xl p-8 border border-slate-800 hover:border-slate-700 hover:bg-slate-800/60 transition group"
                >
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr flex items-center justify-center mb-6 shadow-md" :class="f.color">
                        <component :is="f.icon" class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-xl font-bold text-white group-hover:text-indigo-300 transition">{{ f.name }}</h3>
                    <p class="text-sm text-slate-400 mt-2.5 leading-relaxed">{{ f.desc }}</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-800/80 py-12 text-center text-xs text-slate-400">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-white text-sm">KinderPay</span>
                    <span>&copy; 2026 KinderPay SaaS Malaysia. All rights reserved.</span>
                </div>
                <div class="flex items-center space-x-6 text-slate-400">
                    <Link :href="route('login')" class="hover:text-white transition">Sign In</Link>
                    <a href="#features" class="hover:text-white transition">Features</a>
                    <span class="text-slate-600">|</span>
                    <span class="text-indigo-400">Laravel 13 + Inertia + Vue 3</span>
                </div>
            </div>
        </footer>
    </div>
</template>
