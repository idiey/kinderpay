# KinderPay — Project Roadmap

**Version**: 1.0
**Date**: 2026-09-20
**Author**: Ed (Solution Architect)
**Status**: Draft

---

## Delivery Strategy

KinderPay is built in **4 phases**, each delivering a usable increment. Each phase ends with a deployable release that can be tested with real users.

**Development Approach**: Laravel + Inertia + Vue (monolith), TDD where practical, Docker deployment.

---

## Phase Overview

```
Phase 1: Core Billing        ──────────  4-5 weeks
Phase 2: Staff & Leave        ─────────  2-3 weeks
Phase 3: Payroll              ─────────  3-4 weeks
Phase 4: Finance & Polish     ─────────  2-3 weeks
                                         ──────────
                              Total:     11-15 weeks
```

---

## Phase 1: Core Billing (Weeks 1-5)

> **Goal**: Parents can receive invoices and pay online. Admin can track payments.

### Milestone 1.1 — Project Setup (Week 1)

| Task | Details |
|---|---|
| Laravel 13 project scaffold | Breeze + Inertia + Vue + Tailwind |
| Database design & migrations | kindergartens, users, students, guardians, class_groups |
| Multi-tenancy setup | Global scope trait, middleware |
| Auth & roles | Fortify + Spatie Permission |
| Docker + Render config | Dockerfile, render.yaml, entrypoint.sh |
| PWA setup | Manifest, service worker, offline fallback |
| CI/CD pipeline | GitHub Actions (lint, test, build) |

### Milestone 1.2 — Student Management (Week 2)

| Task | Details |
|---|---|
| Student CRUD | Create, Read, Update, list with filters |
| Guardian CRUD | Link to students, primary contact |
| Class group CRUD | Create groups, assign students |
| Student dashboard | Enrollment stats widget |

### Milestone 1.3 — Fee & Invoicing (Weeks 3-4)

| Task | Details |
|---|---|
| Fee template CRUD | Recurring & one-time fee types |
| Fee assignment | Assign templates to students/classes |
| Invoice generation | Manual + auto (cron) monthly generation |
| Invoice PDF | DomPDF with kindergarten branding |
| Invoice management | List, filter, status tracking |
| Invoice notification | Email to parent on send |

### Milestone 1.4 — Payment & Parent Portal (Week 5)

| Task | Details |
|---|---|
| Billplz integration | Create bill, redirect, webhook callback |
| Payment reconciliation | Auto-match callback to invoice |
| Manual payment recording | Cash/transfer/cheque entry |
| Receipt PDF generation | Auto on payment completion |
| Parent portal | Login, view invoices, pay, download receipts |
| Payment dashboard | Daily/monthly collection summary |

### Phase 1 Exit Criteria
- [ ] Admin can register students and parents
- [ ] Admin can set up fees and generate invoices
- [ ] Parents can log in and pay via FPX
- [ ] Payments auto-reconcile and receipts generate
- [ ] Admin sees payment dashboard
- [ ] Deployed to Render (staging)

---

## Phase 2: Staff & Leave (Weeks 6-8)

> **Goal**: Staff HR records and leave management operational.

### Milestone 2.1 — Staff Management (Week 6)

| Task | Details |
|---|---|
| Staff CRUD | Full profile with encrypted PII |
| Employment details | Position, type, salary, bank details |
| Statutory IDs | EPF, SOCSO, tax number storage |
| Staff directory | List with filters |

### Milestone 2.2 — Leave Management (Weeks 7-8)

| Task | Details |
|---|---|
| Leave type configuration | Admin setup with entitlement rules |
| Leave balance initialization | Auto-calculate based on tenure |
| Leave application | Staff applies via web/mobile |
| Approval workflow | Admin approve/reject with notification |
| Leave balance tracking | Real-time balance update |
| Leave calendar | Team calendar view |
| Public holiday calendar | Malaysian holidays, admin-editable |
| Unpaid leave flagging | Flag for payroll deduction |

### Phase 2 Exit Criteria
- [ ] Staff profiles fully managed with encrypted data
- [ ] Leave types configured with entitlement rules
- [ ] Staff can apply for leave, admin can approve/reject
- [ ] Leave balances auto-tracked
- [ ] Unpaid leave days flagged for payroll

---

## Phase 3: Payroll (Weeks 9-12)

> **Goal**: Monthly payroll processing with Malaysian statutory compliance.

### Milestone 3.1 — Payroll Engine (Weeks 9-10)

| Task | Details |
|---|---|
| Statutory rate tables | Seed EPF/SOCSO/EIS 2026 rates |
| Rate table admin UI | View and edit rates |
| Allowance configuration | Types + per-staff assignment |
| Payroll calculation service | Gross → deductions → net pipeline |
| EPF calculation | Employee + employer, Category 1/2 |
| SOCSO calculation | Wage table lookup |
| EIS calculation | Capped at RM5,000 |
| Unpaid leave integration | Auto-fetch from leave module |

### Milestone 3.2 — Payroll UI & Reports (Weeks 11-12)

| Task | Details |
|---|---|
| Payroll run UI | Generate → Review → Confirm workflow |
| Payroll item editing | Override individual calculations |
| Payslip PDF | Per-staff branded payslip |
| Payroll summary | Totals, statutory contribution totals |
| Payroll history | Past runs, locked after confirm |
| EPF Borang A export | Monthly contribution CSV/Excel |
| SOCSO report export | Monthly report |
| EIS report export | Monthly report |

### Phase 3 Exit Criteria
- [ ] Monthly payroll generates correctly for all staff
- [ ] EPF/SOCSO/EIS calculated per Malaysian rates
- [ ] Unpaid leave auto-deducted from salary
- [ ] Payslip PDFs generated and downloadable
- [ ] Statutory reports exportable
- [ ] Payroll audit trail maintained

---

## Phase 4: Finance Dashboard & Polish (Weeks 13-15)

> **Goal**: Unified financial view and production polish.

### Milestone 4.1 — Finance Dashboard (Week 13)

| Task | Details |
|---|---|
| Revenue dashboard | Billed, collected, outstanding, overdue cards + chart |
| Expense dashboard | Payroll cost, statutory cost |
| P&L summary | Revenue minus payroll expenses |
| Ageing report | Outstanding by 30/60/90 day buckets |
| Collection rate metric | % collected vs billed |
| Export to Excel | All reports downloadable |

### Milestone 4.2 — Polish & Hardening (Weeks 14-15)

| Task | Details |
|---|---|
| Setup wizard | Guided onboarding for new kindergartens |
| Email templates | Branded transactional emails |
| Notification system | In-app notification center |
| Error handling | Graceful error pages, validation messages |
| Performance tuning | Query optimization, caching |
| Security audit | PDPA compliance check, penetration basics |
| Documentation | Admin user guide, parent FAQ |
| Production deploy | Render production environment |
| Seed demo data | Demo kindergarten for sales/trials |

### Phase 4 Exit Criteria
- [ ] Finance dashboard shows complete picture (revenue + expenses)
- [ ] Setup wizard guides new kindergarten through configuration
- [ ] All PDFs branded and professional
- [ ] Production deployed and accessible
- [ ] Demo account available for trials

---

## Future Roadmap (Post-MVP)

| Feature | Phase | Priority |
|---|---|---|
| WhatsApp notifications (Fonnte) | v1.1 | High |
| Billplz card + e-wallet payment | v1.1 | High |
| Student attendance (check-in/out) | v1.2 | Medium |
| BM language support | v1.2 | Medium |
| Credit notes & refunds | v1.2 | Medium |
| Native mobile app (Flutter/React Native) | v2.0 | Medium |
| Multi-branch support | v2.0 | Medium |
| Accounting integration (Xero) | v2.0 | Low |
| Government form submission (e-KWSP, e-Caruman) | v2.0 | Low |
| Student progress / report cards | v2.0 | Low |
| PCB auto-calculation (LHDN API) | v2.0 | Low |
| Yearly Borang EA generation | v1.2 | Medium |
| Bulk salary transfer (bank file export) | v2.0 | Low |
| Parent communication / messaging | v2.0 | Low |

---

## Risk Register

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Statutory rate calculation errors | Medium | High | Table-driven rates, test with sample data from KWSP website |
| Billplz API changes | Low | Medium | Abstract behind `BillplzService`, easy to swap |
| Scope creep (too many features) | High | High | Strict phase gates, P0-only for MVP |
| Single developer bottleneck | Medium | High | Clean code, documentation, modular design |
| Low initial adoption | Medium | Medium | Free tier for small kindergartens, demo instance |

---

## Dependencies

| Dependency | Required By | Lead Time |
|---|---|---|
| Billplz API account + Collection | Phase 1 Milestone 1.4 | 1-3 business days |
| Domain name (kinderpay.my / .com) | Phase 4 production | Order early |
| Render.com account | Phase 1 Milestone 1.1 | Immediate (free tier) |
| SMTP service (Mailgun/SES) | Phase 1 Milestone 1.3 | 1 day setup |
| EPF/SOCSO/EIS rate tables (2026) | Phase 3 Milestone 3.1 | Research from official sites |
| SSL certificate | Phase 4 production | Auto via Render/Cloudflare |

---

*Document ends.*
