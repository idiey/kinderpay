# KinderPay — Technical Architecture Document

**Version**: 1.0
**Date**: 2026-09-20
**Author**: Ed (Solution Architect)
**Status**: Draft

---

## 1. Architecture Overview

KinderPay follows a **monolithic Laravel application** architecture using the Inertia.js + Vue.js pattern for a single-page-app feel with server-side routing. This is a deliberate choice for MVP speed — the same architecture proven in HomePlus and PSLS projects.

### 1.1 High-Level Architecture

```
┌──────────────────────────────────────────────────────┐
│                    CLIENT (Browser)                  │
│  ┌────────────────────────────────────────────────┐  │
│  │  Vue 3 + Inertia.js (SPA-like experience)     │  │
│  │  Tailwind CSS + Headless UI                    │  │
│  │  PWA (Service Worker + Manifest)               │  │
│  └────────────────────────────────────────────────┘  │
└──────────────────────┬───────────────────────────────┘
                       │ HTTPS
┌──────────────────────▼───────────────────────────────┐
│                 APPLICATION SERVER                    │
│  ┌────────────────────────────────────────────────┐  │
│  │  Laravel 13 (PHP 8.3+)                        │  │
│  │  ├── Inertia.js Server Adapter                │  │
│  │  ├── Fortify Authentication                   │  │
│  │  ├── Spatie Permission (Roles & ACL)          │  │
│  │  ├── Laravel Queues (Redis / database)        │  │
│  │  ├── Task Scheduler (cron)                    │  │
│  │  └── DomPDF (invoice/payslip generation)      │  │
│  └────────────────────────────────────────────────┘  │
└───────┬──────────┬──────────┬────────────────────────┘
        │          │          │
   ┌────▼───┐ ┌───▼────┐ ┌──▼──────────────┐
   │ MySQL  │ │ Redis  │ │ File Storage    │
   │ 8.x   │ │ Cache  │ │ (S3/local disk) │
   │        │ │ Queue  │ │ PDFs, uploads   │
   └────────┘ └────────┘ └─────────────────┘
        │
   ┌────▼──────────────────────────────────┐
   │          EXTERNAL SERVICES            │
   │  ├── Billplz (FPX/Card payments)     │
   │  ├── SMTP (Mailgun / SES)            │
   │  ├── WhatsApp API (Fonnte) [future]  │
   │  └── S3 (document storage) [future]  │
   └───────────────────────────────────────┘
```

---

## 2. Tech Stack

| Layer | Technology | Rationale |
|---|---|---|
| **Language** | PHP 8.3+ | Laravel ecosystem, team familiarity |
| **Framework** | Laravel 13 | Proven, batteries-included, strong ecosystem |
| **Frontend** | Vue 3 + Inertia.js | SPA feel without separate API, shared with HomePlus/PSLS |
| **CSS** | Tailwind CSS 3 | Utility-first, fast prototyping |
| **UI Components** | Headless UI / PrimeVue | Accessible, customizable |
| **Database** | MySQL 8.x | Production. SQLite for local dev fallback |
| **Cache/Queue** | Redis | Session, cache, queue driver |
| **Auth** | Laravel Fortify + Breeze | Built-in, supports MFA |
| **Authorization** | Spatie Laravel-Permission | Role & permission management |
| **PDF** | DomPDF / Browsershot | Invoice, receipt, payslip generation |
| **Payment** | Billplz API | Malaysian FPX + card, simple integration |
| **Email** | Mailgun / Amazon SES | Transactional emails |
| **File Storage** | Local disk → S3 | Upload storage, PDF archive |
| **PWA** | Workbox + Web Manifest | Installable, offline cached views |
| **Deploy** | Docker + Render.com | Free tier start, upgrade to DigitalOcean/AWS later |

---

## 3. Multi-Tenancy Strategy

### 3.1 Approach: Single Database, Tenant Column

Each table has a `kindergarten_id` foreign key. All queries scoped via Laravel Global Scopes.

```php
// App\Models\Traits\BelongsToKindergarten.php
trait BelongsToKindergarten
{
    protected static function bootBelongsToKindergarten()
    {
        static::addGlobalScope('kindergarten', function ($query) {
            if (auth()->check()) {
                $query->where('kindergarten_id', auth()->user()->kindergarten_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->kindergarten_id = auth()->user()->kindergarten_id;
            }
        });
    }
}
```

### 3.2 Why Single-DB Over DB-Per-Tenant

| Factor | Single DB | DB Per Tenant |
|---|---|---|
| Simplicity | ✅ Simple | ❌ Complex provisioning |
| Migrations | ✅ Run once | ❌ Run per tenant |
| Cost | ✅ One DB instance | ❌ Multiple DBs or schemas |
| Data isolation | ⚠️ Logical (scope) | ✅ Physical |
| Scale limit | ~500-1000 tenants | Unlimited |

For MVP targeting 50-200 kindergartens, single-DB is the right call. Migrate to DB-per-tenant if we hit scale issues.

---

## 4. Application Modules

### 4.1 Module Structure

```
app/
├── Models/
│   ├── Kindergarten.php
│   ├── User.php
│   ├── Student.php
│   ├── Guardian.php
│   ├── ClassGroup.php
│   ├── FeeTemplate.php
│   ├── Invoice.php
│   ├── InvoiceItem.php
│   ├── Payment.php
│   ├── Staff.php
│   ├── LeaveType.php
│   ├── LeaveBalance.php
│   ├── LeaveRequest.php
│   ├── PayrollRun.php
│   ├── PayrollItem.php
│   ├── Allowance.php
│   └── StatutoryRate.php
├── Http/Controllers/
│   ├── StudentController.php
│   ├── GuardianController.php
│   ├── FeeController.php
│   ├── InvoiceController.php
│   ├── PaymentController.php
│   ├── StaffController.php
│   ├── LeaveController.php
│   ├── PayrollController.php
│   ├── FinanceController.php
│   ├── ParentPortalController.php
│   └── SettingsController.php
├── Services/
│   ├── InvoiceService.php          # Invoice generation logic
│   ├── PaymentService.php          # Payment processing & reconciliation
│   ├── BillplzService.php          # Payment gateway integration
│   ├── PayrollService.php          # Salary calculation engine
│   ├── StatutoryService.php        # EPF/SOCSO/EIS/PCB calculations
│   ├── LeaveService.php            # Leave balance & deduction logic
│   ├── PdfService.php              # PDF generation (invoice, payslip, receipt)
│   └── NotificationService.php    # Email/in-app notification dispatch
├── Jobs/
│   ├── GenerateMonthlyInvoices.php # Scheduled: 1st of month
│   ├── SendPaymentReminders.php    # Scheduled: configurable intervals
│   └── ProcessPaymentCallback.php  # Queue: async payment reconciliation
└── Console/Commands/
    ├── InvoiceGenerate.php
    ├── PayrollRun.php
    └── LeaveBalanceReset.php
```

### 4.2 Frontend Structure

```
resources/js/
├── Pages/
│   ├── Dashboard.vue
│   ├── Students/
│   │   ├── Index.vue
│   │   ├── Create.vue
│   │   ├── Show.vue
│   │   └── Edit.vue
│   ├── Fees/
│   │   ├── Templates.vue
│   │   └── Assign.vue
│   ├── Invoices/
│   │   ├── Index.vue
│   │   ├── Show.vue
│   │   └── Create.vue
│   ├── Payments/
│   │   ├── Index.vue
│   │   └── Record.vue
│   ├── Staff/
│   │   ├── Index.vue
│   │   ├── Create.vue
│   │   ├── Show.vue
│   │   └── Edit.vue
│   ├── Leave/
│   │   ├── Index.vue
│   │   ├── Apply.vue
│   │   ├── Approve.vue
│   │   └── Calendar.vue
│   ├── Payroll/
│   │   ├── Index.vue
│   │   ├── Run.vue
│   │   ├── Review.vue
│   │   └── Payslip.vue
│   ├── Finance/
│   │   ├── Dashboard.vue
│   │   ├── Revenue.vue
│   │   └── Expenses.vue
│   ├── ParentPortal/
│   │   ├── Dashboard.vue
│   │   ├── Invoices.vue
│   │   ├── Pay.vue
│   │   └── History.vue
│   └── Settings/
│       ├── General.vue
│       ├── Fees.vue
│       ├── LeavePolicy.vue
│       ├── PayrollRates.vue
│       └── Users.vue
├── Components/
│   ├── Layout/
│   ├── Tables/
│   ├── Forms/
│   ├── Charts/
│   └── Pdf/
└── Composables/
    ├── usePayroll.js
    ├── useInvoice.js
    └── useLeave.js
```

---

## 5. Payment Integration

### 5.1 Billplz Flow

```
Parent clicks "Pay Now"
  → App creates Billplz Bill (API call)
  → Redirect parent to Billplz payment page
  → Parent completes FPX / Card payment
  → Billplz sends callback (webhook) to our endpoint
  → App verifies callback signature (X-Signature)
  → App updates invoice status → "paid"
  → App generates receipt PDF
  → App sends receipt notification to parent
```

### 5.2 Webhook Endpoint

```
POST /api/webhooks/billplz
  - Verify X-Signature header
  - Find matching invoice by bill reference
  - Update payment record
  - Mark invoice as paid (or partially paid)
  - Dispatch receipt generation job
```

### 5.3 Manual Payment Recording

For cash/bank transfer payments:
- Admin selects invoice → "Record Payment"
- Enters amount, date, method (cash/transfer/cheque), reference number
- Optional: upload proof (bank slip photo)
- Invoice status updates accordingly

---

## 6. Payroll Engine

### 6.1 Calculation Pipeline

```
Input:
  ├── Staff basic salary
  ├── Configured allowances
  ├── Unpaid leave days (from Leave module)
  └── Statutory rate tables

Processing:
  ├── Gross = Basic + Allowances + Overtime
  ├── Unpaid Deduction = (Basic / working_days) × unpaid_days
  ├── Adjusted Gross = Gross - Unpaid Deduction
  ├── EPF Employee = Adjusted Gross × 11%
  ├── EPF Employer = Adjusted Gross × 12% (or 13% if ≤ RM5,000)
  ├── SOCSO = Lookup wage table → employee + employer portions
  ├── EIS Employee = min(Adjusted Gross, 5000) × 0.2%
  ├── EIS Employer = min(Adjusted Gross, 5000) × 0.2%
  ├── PCB = Manual entry or lookup (future: auto-calc via LHDN API)
  └── Net Pay = Adjusted Gross - EPF_emp - SOCSO_emp - EIS_emp - PCB

Output:
  ├── PayrollItem record per staff
  ├── Payslip PDF
  └── Summary report (totals, statutory totals)
```

### 6.2 Statutory Rate Tables

Stored as database config, admin-editable:

```
statutory_rates table:
  - type: epf_employee | epf_employer | socso_employee | socso_employer | eis_employee | eis_employer
  - wage_from: decimal
  - wage_to: decimal
  - rate_type: percentage | fixed
  - rate_value: decimal
  - effective_from: date
  - effective_to: date (nullable = current)
```

---

## 7. Scheduled Jobs

| Job | Schedule | Description |
|---|---|---|
| `invoice:generate` | 1st of month, 00:01 | Auto-generate invoices for all active students |
| `payment:remind` | Daily, 09:00 | Send reminders for overdue invoices (7/14/30 day) |
| `leave:reset` | 1st Jan, 00:01 | Reset annual leave balances (with carry-forward) |
| `backup:run` | Daily, 02:00 | Database backup to S3 |

---

## 8. Security

### 8.1 Authentication & Authorization

- Laravel Fortify: login, registration, password reset, email verification
- Optional: 2FA via TOTP (Google Authenticator)
- Spatie Permission: roles (super_admin, admin, accounts, teacher, parent)
- Middleware-enforced route protection
- Tenant isolation via Global Scopes

### 8.2 Data Protection (PDPA)

- PII fields (IC number, bank details) encrypted at rest (Laravel Crypt)
- HTTPS enforced (TLS 1.2+)
- Password hashing (bcrypt)
- Session timeout: 30 minutes idle
- Audit log for all financial transactions
- Data export capability (PDPA right of access)
- Data deletion capability (PDPA right of erasure)

### 8.3 Payment Security

- No card data stored on our servers (PCI-DSS compliance via Billplz)
- Webhook signature verification
- CSRF protection on all forms
- Rate limiting on payment endpoints

---

## 9. Deployment

### 9.1 Initial: Render.com (Docker)

```
Dockerfile
├── PHP 8.3 + required extensions
├── Nginx
├── Node.js (build assets)
├── Supervisor (queue worker)
└── Cron (scheduler)

render.yaml
├── Web service (Dockerfile)
├── Redis (Render managed)
├── MySQL (Render managed or PlanetScale)
└── Environment variables
```

### 9.2 Future Scale: DigitalOcean / AWS

- App: DigitalOcean App Platform or EC2
- DB: Managed MySQL (DO / RDS)
- Cache: Managed Redis (DO / ElastiCache)
- Storage: S3 / DO Spaces
- CDN: Cloudflare

---

## 10. Monitoring & Observability

| Tool | Purpose |
|---|---|
| Laravel Telescope | Local debug (queries, jobs, exceptions) |
| Sentry | Error tracking (production) |
| Laravel Pulse | Server metrics & performance |
| UptimeRobot | Uptime monitoring |
| Render Logs | Application logs |

---

*Document ends.*
