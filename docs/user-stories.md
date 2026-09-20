# KinderPay — User Stories

**Version**: 1.0
**Date**: 2026-09-20
**Author**: Ed (Solution Architect)
**Status**: Draft

---

## Legend

| Priority | Meaning |
|---|---|
| **P0** | Must-have for MVP |
| **P1** | Important, include if time permits |
| **P2** | Nice-to-have, post-MVP |

---

## Epic 1: Student Management

### US-1.1 — Register New Student [P0]
**As an** admin,
**I want to** register a new student with their profile details,
**so that** I can track enrollment and assign fees.

**Acceptance Criteria:**
- Form captures: name, IC/MyKid, DOB, gender, photo, allergies, medical notes
- Student defaults to "active" status
- At least one guardian must be linked

### US-1.2 — Register Parent/Guardian [P0]
**As an** admin,
**I want to** register parents/guardians and link them to students,
**so that** I can contact them and send invoices.

**Acceptance Criteria:**
- Capture: name, IC, phone, email, WhatsApp, relationship, address
- Support multiple guardians per student
- Mark one as primary contact

### US-1.3 — Assign Student to Class [P0]
**As an** admin,
**I want to** assign students to class groups,
**so that** I can organize by age/level and assign teachers.

**Acceptance Criteria:**
- Create class groups with name and academic year
- Drag-drop or select-based assignment
- Show class capacity vs current enrollment

### US-1.4 — View Student List [P0]
**As an** admin,
**I want to** see a filterable list of all students,
**so that** I can quickly find and manage student records.

**Acceptance Criteria:**
- Filter by: class, status (active/withdrawn/graduated), search by name
- Show: name, class, parent contact, enrollment date, status
- Click to view full profile

### US-1.5 — Bulk Import Students [P2]
**As an** admin,
**I want to** import students from an Excel/CSV file,
**so that** I can migrate existing records quickly.

**Acceptance Criteria:**
- Download template CSV
- Upload and preview before import
- Show validation errors per row
- Skip duplicates (match by IC number)

---

## Epic 2: Fee & Billing

### US-2.1 — Create Fee Template [P0]
**As an** admin,
**I want to** define fee types with amounts and frequency,
**so that** I can standardize billing across students.

**Acceptance Criteria:**
- Configure: name, type (recurring/one-time), amount, frequency (monthly/quarterly/yearly/once)
- Examples: "Monthly Tuition RM350", "Registration Fee RM200", "Meal Plan RM100/month"
- Activate/deactivate templates

### US-2.2 — Assign Fees to Students [P0]
**As an** admin,
**I want to** assign fee templates to individual students or entire classes,
**so that** invoices are generated correctly.

**Acceptance Criteria:**
- Assign one or more fee templates per student
- Override amount for individual students (e.g., discount)
- Bulk assign to entire class
- Set effective date range

### US-2.3 — Auto-Generate Monthly Invoices [P0]
**As an** admin,
**I want** the system to automatically generate invoices on the 1st of each month,
**so that** I don't have to manually create them.

**Acceptance Criteria:**
- Cron job runs on configured day (default: 1st)
- Creates invoice per student with all assigned recurring fees
- Invoice number auto-generated: INV-YYYY-MM-NNN
- Status defaults to "draft" for review before sending

### US-2.4 — Review and Send Invoices [P0]
**As an** admin,
**I want to** review draft invoices and send them to parents,
**so that** I can verify accuracy before billing.

**Acceptance Criteria:**
- View all draft invoices for the month
- Edit line items before sending
- "Send All" or individual send
- Parents receive notification (email + in-app)

### US-2.5 — Generate Invoice PDF [P0]
**As an** admin,
**I want** invoices to be available as branded PDF documents,
**so that** parents have official records.

**Acceptance Criteria:**
- PDF includes: kindergarten logo/name, student name, line items, total, due date
- Downloadable by admin and parent
- Auto-attached to email notification

### US-2.6 — Add Ad-Hoc Charges [P1]
**As an** admin,
**I want to** add one-time charges to a student's invoice,
**so that** I can bill for special activities or purchases.

**Acceptance Criteria:**
- Add charge with description and amount to existing or new invoice
- Examples: field trip, concert costume, replacement uniform

### US-2.7 — Apply Discounts [P1]
**As an** admin,
**I want to** apply discounts to specific students,
**so that** sibling discounts and scholarships are reflected.

**Acceptance Criteria:**
- Fixed amount or percentage discount
- Per fee template or per invoice
- Reason field (sibling, scholarship, early payment)

### US-2.8 — Overdue Payment Reminders [P1]
**As an** admin,
**I want** the system to automatically remind parents of overdue invoices,
**so that** I don't have to chase payments manually.

**Acceptance Criteria:**
- Configurable reminder intervals (7, 14, 30 days after due date)
- Auto-send email/notification to parent
- Admin dashboard shows overdue count and total

---

## Epic 3: Payment Collection

### US-3.1 — Pay Online via FPX [P0]
**As a** parent,
**I want to** pay my child's fees online via bank transfer (FPX),
**so that** I can pay conveniently without visiting the kindergarten.

**Acceptance Criteria:**
- "Pay Now" button on invoice in parent portal
- Redirects to Billplz payment page
- After payment, auto-redirect back with confirmation
- Invoice status updates to "paid"

### US-3.2 — Record Manual Payment [P0]
**As an** admin,
**I want to** record cash or bank transfer payments,
**so that** all payments are tracked regardless of method.

**Acceptance Criteria:**
- Select invoice → "Record Payment"
- Enter: amount, date, method (cash/transfer/cheque), reference number
- Optional: upload bank slip photo
- Invoice balance updates automatically

### US-3.3 — Auto-Reconcile Online Payments [P0]
**As a** system,
**I want to** automatically match payment gateway callbacks to invoices,
**so that** invoice statuses are updated without manual intervention.

**Acceptance Criteria:**
- Webhook receives Billplz callback
- Verify signature for security
- Match to invoice by reference
- Update payment status and invoice status
- Generate receipt

### US-3.4 — Generate Payment Receipt [P0]
**As a** parent,
**I want to** receive a payment receipt after paying,
**so that** I have proof of payment for my records.

**Acceptance Criteria:**
- Receipt PDF auto-generated on payment completion
- Includes: receipt number, payment date, amount, method, invoice reference
- Downloadable from parent portal
- Sent via email notification

### US-3.5 — View Payment History [P0]
**As an** admin,
**I want to** see all payments with filters,
**so that** I can track collections and reconcile.

**Acceptance Criteria:**
- Filter by: date range, method, status, student
- Show: date, student, amount, method, reference, status
- Daily collection summary total
- Export to Excel

---

## Epic 4: Staff Management

### US-4.1 — Register Staff [P0]
**As an** admin,
**I want to** add staff members with their employment details,
**so that** I can manage HR and payroll.

**Acceptance Criteria:**
- Capture: name, IC, DOB, gender, phone, email, address
- Employment: position, type (full-time/part-time), join date, basic salary
- Banking: bank name, account number
- Statutory: EPF number, SOCSO number, tax number
- All sensitive fields encrypted at rest

### US-4.2 — View Staff Directory [P0]
**As an** admin,
**I want to** see a list of all staff with key details,
**so that** I can manage the team.

**Acceptance Criteria:**
- Filter by: position, status, employment type
- Show: name, position, phone, join date, status
- Click to view full profile

### US-4.3 — Update Staff Salary [P0]
**As an** admin,
**I want to** update a staff member's salary and allowances,
**so that** payroll reflects current compensation.

**Acceptance Criteria:**
- Edit basic salary with effective date
- Add/remove/edit allowances
- Salary history maintained (audit trail)

---

## Epic 5: Leave Management

### US-5.1 — Configure Leave Policy [P0]
**As an** admin,
**I want to** set up leave types and entitlements,
**so that** the system correctly tracks leave balances.

**Acceptance Criteria:**
- Create leave types: Annual, MC, Emergency, Unpaid, Maternity, Paternity, Replacement
- Set entitlement by tenure (e.g., <2 years = 8 days, 2-5 years = 12 days)
- Configure: paid/unpaid, requires attachment, carry-forward rules

### US-5.2 — Apply for Leave [P0]
**As a** staff member,
**I want to** submit a leave request from my phone,
**so that** I don't need paper forms.

**Acceptance Criteria:**
- Select: leave type, start date, end date, reason
- System auto-calculates working days (excl. weekends/public holidays)
- Attach MC or supporting document (photo/PDF)
- Show current balance for selected leave type
- Warn if balance insufficient

### US-5.3 — Approve/Reject Leave [P0]
**As an** admin,
**I want to** review and approve/reject leave requests,
**so that** I can manage staffing.

**Acceptance Criteria:**
- View pending requests with details
- Approve or reject with optional comment
- Staff receives notification of decision
- Balance auto-updated on approval

### US-5.4 — View Leave Balance [P0]
**As a** staff member,
**I want to** see my current leave balances,
**so that** I know how many days I have remaining.

**Acceptance Criteria:**
- Dashboard widget showing all leave types with: entitled, used, balance
- Leave history list (all past requests with status)

### US-5.5 — Leave Calendar [P1]
**As an** admin,
**I want to** see a team calendar showing who is on leave,
**so that** I can plan coverage.

**Acceptance Criteria:**
- Calendar view (month) showing approved leave per staff
- Color-coded by leave type
- Click to view leave details
- Include public holidays

### US-5.6 — Public Holiday Calendar [P1]
**As an** admin,
**I want to** configure public holidays,
**so that** leave calculations exclude them correctly.

**Acceptance Criteria:**
- Pre-loaded Malaysian public holidays
- Admin can add/edit state-specific holidays
- Leave day calculation respects holiday calendar

---

## Epic 6: Payroll

### US-6.1 — Run Monthly Payroll [P0]
**As an** admin,
**I want to** run payroll for all staff in one click,
**so that** salaries are calculated correctly and consistently.

**Acceptance Criteria:**
- Select month/year → "Generate Payroll"
- System calculates for all active staff:
  - Basic salary + allowances
  - Unpaid leave deductions (auto-fetched from leave module)
  - EPF, SOCSO, EIS (employee + employer portions)
  - PCB (manual entry for now)
  - Net pay
- Status starts as "Draft" for review

### US-6.2 — Review Payroll Before Confirming [P0]
**As an** admin,
**I want to** review the payroll summary before confirming,
**so that** I can catch errors before finalizing.

**Acceptance Criteria:**
- Table showing all staff with: gross, deductions breakdown, net pay
- Edit individual items (override amounts)
- Total summary: total gross, total deductions, total net, total employer cost
- "Confirm" button to lock payroll

### US-6.3 — Generate Payslip PDF [P0]
**As a** staff member,
**I want to** receive my monthly payslip,
**so that** I have a record of my salary details.

**Acceptance Criteria:**
- Payslip includes: basic salary, allowances, gross, all deduction line items, net pay
- Shows employer EPF/SOCSO/EIS contributions
- Branded with kindergarten name
- Downloadable by staff and admin

### US-6.4 — View Payroll History [P0]
**As an** admin,
**I want to** see past payroll runs,
**so that** I can reference historical data.

**Acceptance Criteria:**
- List of payroll runs by month/year with status and totals
- Click to view details and individual payslips
- Cannot edit confirmed payroll (audit integrity)

### US-6.5 — Statutory Reports [P1]
**As an** admin,
**I want to** generate EPF/SOCSO/EIS monthly reports,
**so that** I can submit contributions to the respective agencies.

**Acceptance Criteria:**
- EPF Borang A format: employee name, IC, EPF#, wages, employee+employer contribution
- SOCSO report: similar format
- EIS report: similar format
- Export to Excel/CSV for manual submission

### US-6.6 — Yearly EA Form [P2]
**As an** admin,
**I want to** generate yearly Borang EA for each staff,
**so that** staff can file their income tax.

**Acceptance Criteria:**
- Summarizes full year: total salary, total EPF, total SOCSO, total tax
- Standard Borang EA format
- PDF per staff, bulk download

---

## Epic 7: Finance Dashboard

### US-7.1 — Revenue Dashboard [P0]
**As an** admin,
**I want to** see my kindergarten's financial health at a glance,
**so that** I can make informed decisions.

**Acceptance Criteria:**
- Cards: total billed this month, total collected, outstanding, overdue
- Bar chart: monthly revenue trend (6-12 months)
- Collection rate percentage
- Top overdue accounts list

### US-7.2 — Expense Dashboard [P0]
**As an** admin,
**I want to** see total payroll costs,
**so that** I understand my expenses.

**Acceptance Criteria:**
- Cards: total salary cost, total statutory employer contributions
- Monthly payroll trend chart
- Breakdown by staff (table)

### US-7.3 — P&L Summary [P1]
**As an** admin,
**I want to** see a simple profit & loss view,
**so that** I know if my kindergarten is profitable.

**Acceptance Criteria:**
- Revenue (fees collected) minus Expenses (payroll + statutory)
- Monthly view
- Note: Only covers fee revenue and payroll expenses (not full accounting)

### US-7.4 — Ageing Report [P1]
**As an** admin,
**I want to** see outstanding invoices grouped by age,
**so that** I can prioritize collections.

**Acceptance Criteria:**
- Buckets: Current, 1-30 days, 31-60 days, 61-90 days, 90+ days
- Per student breakdown
- Total per bucket
- Export to Excel

---

## Epic 8: Parent Portal

### US-8.1 — Parent Login [P0]
**As a** parent,
**I want to** log into the system to manage my child's account,
**so that** I can view bills and make payments.

**Acceptance Criteria:**
- Login with email + password
- First-time setup: admin creates account, parent receives setup email
- Mobile-responsive design (PWA)

### US-8.2 — View My Invoices [P0]
**As a** parent,
**I want to** see all current and past invoices for my child,
**so that** I know what I owe.

**Acceptance Criteria:**
- List of invoices with: month, amount, status, due date
- Color-coded status: green (paid), yellow (pending), red (overdue)
- Click to view full invoice detail

### US-8.3 — Make Payment [P0]
**As a** parent,
**I want to** pay an invoice online,
**so that** I can settle fees without cash or visiting the school.

**Acceptance Criteria:**
- "Pay Now" button on unpaid/partially paid invoices
- Redirects to payment gateway (FPX/card)
- Returns to confirmation page after payment
- Receipt immediately available

### US-8.4 — Download Receipts [P0]
**As a** parent,
**I want to** download payment receipts,
**so that** I can claim from my employer or for tax purposes.

**Acceptance Criteria:**
- Receipt PDF available for each completed payment
- Includes all official details (kindergarten name, SSM, receipt #, amount)
- Accessible from payment history

---

## Epic 9: Settings & Setup

### US-9.1 — Kindergarten Setup Wizard [P0]
**As a** new admin,
**I want** a guided setup process,
**so that** I can configure my kindergarten quickly.

**Acceptance Criteria:**
- Step 1: Kindergarten details (name, address, logo)
- Step 2: Academic year
- Step 3: Create classes
- Step 4: Set up fee templates
- Step 5: Configure leave policy
- Step 6: Payment gateway setup
- Can be skipped and completed later

### US-9.2 — User & Role Management [P0]
**As an** admin,
**I want to** manage user accounts and permissions,
**so that** the right people have the right access.

**Acceptance Criteria:**
- Create/edit/deactivate user accounts
- Assign roles: admin, accounts, teacher, parent
- Role-based menu and access control

### US-9.3 — Statutory Rate Configuration [P0]
**As an** admin,
**I want to** update EPF/SOCSO/EIS rate tables,
**so that** payroll calculations stay current.

**Acceptance Criteria:**
- View current rates in table format
- Edit rates with effective date
- System ships with current (2026) default rates
- Admin can override per kindergarten if needed

---

*Document ends.*
