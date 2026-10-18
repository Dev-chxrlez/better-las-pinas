import { expect, test } from '@playwright/test'

const user = { id: 1, name: 'Maria Santos', email: 'maria@example.test', role: 'resident' }
const categories = [{ id: 'cat-1', name: 'Road Damage' }]
const barangays = [{ id: 'brgy-1', name: 'Pilar' }]
const report = { id: 'report-1', reference: 'LPC-202610-TEST', title: 'Damaged road surface', description: 'A large damaged area is visible near the corner.', location_text: 'Near the main gate and covered court', latitude: null, longitude: null, status: 'submitted', submitted_at: new Date().toISOString(), category: categories[0], barangay: barangays[0], timeline: [] }

async function mockGuest(page: import('@playwright/test').Page) {
  await page.route('**/api/v1/auth/me', route => route.fulfill({ status: 401, contentType: 'application/json', body: JSON.stringify({ message: 'Unauthenticated.' }) }))
}

async function mockResident(page: import('@playwright/test').Page) {
  await page.route('**/api/v1/auth/me', route => route.fulfill({ contentType: 'application/json', body: JSON.stringify({ data: { user } }) }))
  await page.route('**/api/v1/reference-data', route => route.fulfill({ contentType: 'application/json', body: JSON.stringify({ data: { categories, barangays, departments: [] } }) }))
  await page.route('**/api/v1/reports', route => route.fulfill({ status: 201, contentType: 'application/json', body: JSON.stringify({ data: { report } }) }))
  await page.route('**/api/v1/reports/report-1', route => route.fulfill({ contentType: 'application/json', body: JSON.stringify({ data: { report } }) }))
  await page.route('**/sanctum/csrf-cookie', route => route.fulfill({ contentType: 'application/json', body: '{}' }))
}

test('public pages remain usable without an account at required widths', async ({ page }, testInfo) => {
  test.skip(testInfo.project.name !== 'desktop')
  test.setTimeout(90_000)
  await mockGuest(page)
  for (const width of [320, 375, 390, 430, 768, 1440]) {
    await page.setViewportSize({ width, height: 900 })
    for (const path of ['/home', '/map', '/about']) {
      await page.goto(path, { waitUntil: 'domcontentloaded' })
      expect(await page.evaluate(() => document.documentElement.scrollWidth > document.documentElement.clientWidth), `${path} overflowed at ${width}px`).toBeFalsy()
    }
    if (width < 900) await expect(page.locator('.bottom-nav')).toBeVisible()
    else await expect(page.locator('.app-sidebar')).toBeVisible()
  }
})

test('guest onboarding leads from splash to welcome and authentication choices', async ({ page }) => {
  await mockGuest(page)
  await page.goto('/')
  await expect(page.getByRole('heading', { name: 'Better Las Piñas' })).toBeVisible()
  await page.getByRole('button', { name: 'Continue to Better Las Piñas' }).click()
  await expect(page).toHaveURL(/\/welcome$/)
  await expect(page.getByRole('link', { name: /Sign In/ })).toBeVisible()
  await expect(page.getByRole('link', { name: 'Sign Up' })).toBeVisible()
  await page.getByRole('link', { name: /Sign In/ }).click()
  await expect(page.getByRole('button', { name: 'Continue with Google' })).toBeVisible()
  await expect(page.getByRole('button', { name: 'Continue with Facebook' })).toBeVisible()
})

test('protected resident submission redirects guests to sign in', async ({ page }) => {
  await mockGuest(page)
  await page.goto('/report')
  await expect(page).toHaveURL(/\/sign-in$/)
  await expect(page.getByRole('heading', { name: 'Welcome back' })).toBeVisible()
})

test('authenticated resident can complete a persisted report submission', async ({ page }) => {
  test.setTimeout(90_000)
  await mockResident(page)
  await page.goto('/report')
  await expect(page.getByRole('heading', { name: /Issue Details|Tell us what happened/ })).toBeVisible()
  await page.getByLabel(/^Category/).selectOption('cat-1')
  await page.getByLabel(/Report title/).fill('Damaged road surface')
  await page.getByLabel(/^Description/).fill(report.description)
  await page.getByRole('button', { name: 'Continue' }).click()
  await page.getByLabel(/Barangay/).selectOption('brgy-1')
  const locationField = page.getByLabel(/Street, landmark/)
  await locationField.scrollIntoViewIfNeeded()
  await locationField.fill(report.location_text)
  await page.getByRole('button', { name: 'Continue' }).click()
  await page.getByRole('button', { name: 'Continue' }).click()
  await page.getByRole('button', { name: 'Submit report' }).click()
  await expect(page).toHaveURL(/\/my-reports\/report-1$/)
  await expect(page.getByText('LPC-202610-TEST')).toBeVisible()
})

test('resident cannot enter staff workspace', async ({ page }) => {
  await mockResident(page)
  await page.goto('/staff')
  await expect(page).toHaveURL(/\/dashboard$/)
})
