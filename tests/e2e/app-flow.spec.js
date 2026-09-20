import { test, expect } from '@playwright/test';

test.describe('KinderPay Core User Flows', () => {

  test('Public Landing page loads correctly', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle(/KinderPay|Laravel/);
    const loginLink = page.getByRole('link', { name: /log in/i });
    await expect(loginLink).toBeVisible();
  });

  test('Admin Flow: Login, View Dashboard, Students, Invoices, Payroll', async ({ page }) => {
    // 1. Login as Admin
    await page.goto('/login');
    await page.fill('input[type="email"]', 'demo@kinderpay.test');
    await page.fill('input[type="password"]', 'password');
    await page.click('button[type="submit"]');

    // 2. Verify Admin Dashboard
    await expect(page).toHaveURL(/.*dashboard/);
    await expect(page.locator('text=Active Students')).toBeVisible();
    await expect(page.locator('text=This Month Billed')).toBeVisible();

    // 3. Navigate to Students directory
    await page.goto('/students');
    await expect(page.locator('text=Muhammad Rayyan bin Rosli')).toBeVisible();

    // 4. Navigate to Invoices
    await page.goto('/invoices');
    await expect(page.locator('text=Invoices & Billing')).toBeVisible();

    // 5. Navigate to Payroll
    await page.goto('/payroll');
    await expect(page.locator('text=Monthly Payroll')).toBeVisible();

    // 6. Navigate to Financial Reports
    await page.goto('/reports/finance');
    await expect(page.locator('text=Financial Reports & Profitability')).toBeVisible();
  });

  test('Parent Portal Flow: Login, View Children, View Invoice with FPX Button', async ({ page }) => {
    // 1. Login as Parent
    await page.goto('/login');
    await page.fill('input[type="email"]', 'parent@kinderpay.test');
    await page.fill('input[type="password"]', 'password');
    await page.click('button[type="submit"]');

    // 2. Check Parent Portal
    await page.goto('/parent/dashboard');
    await expect(page.locator('text=Parent Portal')).toBeVisible();
    await expect(page.locator('text=Muhammad Rayyan bin Rosli')).toBeVisible();

    // 3. View Invoices
    await page.goto('/parent/invoices');
    await expect(page.locator('text=Tuition Invoices')).toBeVisible();
  });
});
