# KinderPay — Product Requirements Document (PRD)

**Version**: 1.0
**Date**: 2026-09-20
**Author**: Ed (Solution Architect)
**Status**: Draft

---

## 1. Executive Summary

KinderPay is a web-based SaaS platform designed for Malaysian kindergartens (tadika/taska) to manage their core operations: student enrollment, fee billing & payment collection, staff payroll, leave management, and financial reporting. The platform replaces manual spreadsheets and WhatsApp-based billing with a modern, mobile-friendly system.

### 1.1 Problem Statement

Malaysian kindergartens (estimated 10,000+ registered with KEMAS/MOE) face operational challenges:

- **Manual billing** — Invoices created in Excel/Word, sent via WhatsApp screenshots
- **Payment tracking** — Cash/bank transfer receipts tracked manually, prone to errors
- **No financial visibility** — No real-time view of revenue, outstanding, or overdue payments
- **Payroll on spreadsheets** — Salary calculation, statutory deductions (EPF/SOCSO/EIS/PCB) done manually
- **Leave tracking** — Paper forms or WhatsApp messages, no balance tracking
- **Parent frustration** — No self-service portal for payment history or receipts

### 1.2 Solution

KinderPay provides an integrated platform covering:

1. Student & parent management
2. Automated fee invoicing & online payment collection
3. Staff registry, payroll processing & payslip generation
4. Leave application & approval workflow
5. Unified financial dashboard (revenue + expenses)
6. Parent self-service portal

### 1.3 Target Market

- **Primary**: Private kindergartens (tadika swasta) in Malaysia, 20-150 students
- **Secondary**: Childcare centres (taska), enrichment centres
- **Pricing**: SaaS subscription RM49-149/month per centre

---

## 2. User Personas

### 2.1 Admin / Principal (Pengetua)

- **Profile**: Kindergarten owner or principal, 30-55 years old
- **Tech comfort**: Basic — uses WhatsApp, Facebook, basic Excel
- **Pain points**: Spends 5-10 hours/week on billing, chasing payments, calculating payroll
- **Needs**: Simple dashboard, auto-invoicing, payment tracking, payroll in one click
- **Key metric**: Time saved on admin tasks

### 2.2 Teacher / Staff

- **Profile**: Full-time or part-time teacher/assistant, 22-45 years old
- **Tech comfort**: Moderate — comfortable with phone apps
- **Pain points**: No visibility on leave balance, payslip received late or verbal only
- **Needs**: Apply leave from phone, view payslip, see leave balance
- **Key metric**: Transparency and self-service

### 2.3 Parent (Ibu Bapa)

- **Profile**: Working parent, 25-45 years old
- **Tech comfort**: High — daily smartphone user
- **Pain points**: Unclear billing, no receipt for tax/employer claims, cash-only payment
- **Needs**: View bills, pay online (FPX/e-wallet), download receipts
- **Key metric**: Convenience and payment flexibility

---

## 3. Functional Requirements

### 3.1 Student Management

| ID | Requirement | Priority |
|---|---|---|
| STU-001 | Register new student with profile (name, IC/MyKid, DOB, photo, allergies, medical notes) | P0 |
| STU-002 | Register parent/guardian with contact details (phone, email, WhatsApp) | P0 |
| STU-003 | Assign student to class/group | P0 |
| STU-004 | Support multiple guardians per student (father, mother, emergency contact) | P1 |
| STU-005 | Track enrollment status (active, withdrawn, graduated) | P0 |
| STU-006 | Bulk import students from Excel/CSV | P2 |
| STU-007 | Student attendance tracking (daily check-in/check-out) | P2 |

### 3.2 Fee & Billing Management

| ID | Requirement | Priority |
|---|---|---|
| FEE-001 | Define fee templates (monthly tuition, registration, deposit, meals, transport, uniform, activities) | P0 |
| FEE-002 | Assign fee templates to students (individual or by class) | P0 |
| FEE-003 | Auto-generate monthly invoices on configurable date (e.g., 1st of month) | P0 |
| FEE-004 | Support one-time ad-hoc charges (field trip, concert, etc.) | P1 |
| FEE-005 | Apply discounts (sibling discount, early payment, scholarship) | P1 |
| FEE-006 | Invoice PDF generation with kindergarten branding | P0 |
| FEE-007 | Send invoice notification to parent (in-app + email) | P0 |
| FEE-008 | WhatsApp invoice notification | P2 |
| FEE-009 | Track invoice status (draft, sent, partially paid, paid, overdue) | P0 |
| FEE-010 | Overdue payment auto-reminder (configurable: 7, 14, 30 days) | P1 |
| FEE-011 | Credit note / refund support | P2 |
| FEE-012 | Fee statement generation (per student, per period) | P1 |

### 3.3 Payment Collection

| ID | Requirement | Priority |
|---|---|---|
| PAY-001 | Online payment via FPX (bank transfer) | P0 |
| PAY-002 | Online payment via credit/debit card | P1 |
| PAY-003 | E-wallet payment (Touch 'n Go, Boost, GrabPay) | P2 |
| PAY-004 | Manual payment recording (cash, bank transfer, cheque) | P0 |
| PAY-005 | Auto-reconciliation — match payment callback to invoice | P0 |
| PAY-006 | Payment receipt PDF generation | P0 |
| PAY-007 | Partial payment support | P1 |
| PAY-008 | Payment history log (audit trail) | P0 |
| PAY-009 | Daily payment collection summary | P1 |

### 3.4 Staff Management

| ID | Requirement | Priority |
|---|---|---|
| STA-001 | Staff profile (name, IC, position, employment type, join date) | P0 |
| STA-002 | Store bank details (bank name, account number) for salary payment | P0 |
| STA-003 | Store statutory IDs (EPF number, SOCSO number, tax number) | P0 |
| STA-004 | Track employment status (active, resigned, terminated) | P0 |
| STA-005 | Staff roles & permissions (admin, teacher, accounts) | P1 |
| STA-006 | Staff attendance / clock-in/out | P2 |

### 3.5 Payroll

| ID | Requirement | Priority |
|---|---|---|
| PAR-001 | Configure basic salary per staff | P0 |
| PAR-002 | Configure allowances (transport, meal, phone, housing) | P1 |
| PAR-003 | Monthly payroll run (draft → review → confirm) | P0 |
| PAR-004 | Auto-calculate EPF deduction (employee 11% + employer 12/13%) | P0 |
| PAR-005 | Auto-calculate SOCSO deduction (based on wage table) | P0 |
| PAR-006 | Auto-calculate EIS deduction (0.2% employee + 0.2% employer) | P0 |
| PAR-007 | PCB/MTD tax deduction (manual entry or schedule-based) | P1 |
| PAR-008 | Unpaid leave auto-deduction (linked from Leave module) | P0 |
| PAR-009 | Overtime calculation | P2 |
| PAR-010 | Bonus / additional payment | P2 |
| PAR-011 | Payslip PDF generation per staff | P0 |
| PAR-012 | Payroll summary report (total salary expense, statutory contributions) | P0 |
| PAR-013 | EPF monthly contribution report (Borang A) | P1 |
| PAR-014 | SOCSO monthly report | P1 |
| PAR-015 | EIS monthly report | P1 |
| PAR-016 | Yearly EA Form (Borang EA) generation | P2 |
| PAR-017 | Payroll history & audit trail | P0 |

### 3.6 Leave Management

| ID | Requirement | Priority |
|---|---|---|
| LEV-001 | Configure leave types (annual, MC, emergency, unpaid, maternity, paternity, replacement) | P0 |
| LEV-002 | Set default entitlement per leave type (configurable by tenure) | P0 |
| LEV-003 | Staff submits leave request (type, dates, reason, MC attachment) | P0 |
| LEV-004 | Approval workflow (pending → approved/rejected by admin) | P0 |
| LEV-005 | Auto-calculate leave balance (entitled - used = remaining) | P0 |
| LEV-006 | Carry-forward policy configuration | P1 |
| LEV-007 | Leave calendar view (team-wide) | P1 |
| LEV-008 | Annual leave balance reset (configurable: Jan or join-date anniversary) | P1 |
| LEV-009 | Leave history report per staff | P0 |
| LEV-010 | Notify payroll of unpaid leave days | P0 |
| LEV-011 | Half-day leave support | P2 |
| LEV-012 | Public holiday calendar (Malaysian) | P1 |

### 3.7 Finance & Reporting

| ID | Requirement | Priority |
|---|---|---|
| FIN-001 | Revenue dashboard (total collected, outstanding, overdue — by month) | P0 |
| FIN-002 | Expense dashboard (payroll cost, statutory contributions) | P0 |
| FIN-003 | Profit & Loss summary (revenue - payroll expenses) | P1 |
| FIN-004 | Ageing report (outstanding invoices by 30/60/90 days) | P1 |
| FIN-005 | Collection rate metric (% collected vs billed) | P1 |
| FIN-006 | Export reports to Excel/CSV | P1 |
| FIN-007 | Monthly financial summary email to admin | P2 |
| FIN-008 | Year-end summary report | P2 |

### 3.8 Parent Portal

| ID | Requirement | Priority |
|---|---|---|
| PRT-001 | Parent login (email + password) | P0 |
| PRT-002 | View current & past invoices | P0 |
| PRT-003 | Make online payment from portal | P0 |
| PRT-004 | View payment history | P0 |
| PRT-005 | Download receipt PDF | P0 |
| PRT-006 | Download fee statement | P1 |
| PRT-007 | View child's profile & class info | P1 |
| PRT-008 | Receive push/email notifications | P1 |

### 3.9 System & Settings

| ID | Requirement | Priority |
|---|---|---|
| SYS-001 | Kindergarten profile setup (name, logo, address, SSM registration) | P0 |
| SYS-002 | Academic year / term configuration | P1 |
| SYS-003 | User management with roles (super admin, admin, accounts, teacher, parent) | P0 |
| SYS-004 | Payment gateway configuration (API keys) | P0 |
| SYS-005 | Email/notification settings | P1 |
| SYS-006 | Statutory rate tables (EPF, SOCSO, EIS) — admin-editable | P0 |
| SYS-007 | Data backup & export | P2 |
| SYS-008 | Multi-language support (BM / English) | P2 |

---

## 4. Non-Functional Requirements

| Category | Requirement |
|---|---|
| **Performance** | Page load < 2s, dashboard < 3s, payment callback processing < 5s |
| **Availability** | 99.5% uptime (SaaS) |
| **Security** | HTTPS everywhere, PDPA-compliant data handling, encrypted PII at rest, role-based access |
| **Scalability** | Support 500+ kindergartens on shared infrastructure |
| **Mobile** | PWA — responsive design, installable on phone, offline viewing of cached data |
| **Browser** | Chrome, Safari, Edge (latest 2 versions) |
| **Compliance** | Malaysian PDPA (Personal Data Protection Act 2010), payment PCI-DSS via gateway |
| **Backup** | Daily automated database backup, 30-day retention |
| **Audit** | All financial transactions logged with timestamp, user, and action |

---

## 5. Out of Scope (v1)

- Native mobile app (iOS/Android) — PWA first
- Accounting integration (Xero, QuickBooks) — future
- Government reporting submission (e-KWSP, e-PERKESO) — future, manual export first
- Curriculum / lesson planning
- Student progress / report cards
- CCTV integration
- Bus/transport GPS tracking
- Inventory management (supplies, food stock)

---

## 6. Success Metrics

| Metric | Target (6 months post-launch) |
|---|---|
| Registered kindergartens | 50 |
| Paying subscribers | 20 |
| Monthly payment transactions | 500+ |
| Admin time saved per kindergarten | 60% reduction in billing/payroll hours |
| Parent online payment adoption | 40% of total collections |
| NPS score | > 40 |

---

## 7. Risks & Mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| Low tech adoption by kindergarten admins | Low sign-up | Onboarding wizard, BM language, WhatsApp support |
| Payment gateway integration delays | Delayed MVP | Start with Billplz (simplest API), add others later |
| Statutory rate changes (EPF/SOCSO) | Incorrect calculations | Config-driven rate tables, yearly review process |
| PDPA compliance gaps | Legal risk | Data encryption, consent forms, privacy policy, DPO appointment |
| Competition from established players | Market share | Focus on simplicity + MY-specific features (BM, local gateways) |

---

## 8. Glossary

| Term | Definition |
|---|---|
| **Tadika** | Taman Didikan Kanak-Kanak — Kindergarten |
| **Taska** | Taman Asuhan Kanak-Kanak — Childcare Centre |
| **EPF/KWSP** | Employees Provident Fund / Kumpulan Wang Simpanan Pekerja |
| **SOCSO/PERKESO** | Social Security Organisation / Pertubuhan Keselamatan Sosial |
| **EIS/SIP** | Employment Insurance System / Sistem Insurans Pekerjaan |
| **PCB/MTD** | Potongan Cukai Berjadual / Monthly Tax Deduction |
| **FPX** | Financial Process Exchange — Malaysian online banking payment |
| **PDPA** | Personal Data Protection Act 2010 (Malaysia) |
| **Borang A** | EPF monthly contribution form |
| **Borang EA** | Annual employee remuneration statement |

---

*Document ends.*
