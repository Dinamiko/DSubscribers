import {test, expect} from '@playwright/test'

test('user subscribe', async ({page}) => {
    await page.goto('/')

    const name = crypto.randomUUID()

    await page.locator('#form-validation').getByPlaceholder('E-mail').fill(`${name}@example.com`);
    await page.locator('#form-validation').getByRole('button', { name: 'SUBMIT' }).click();

    await page.goto('/wp-admin');
    await page.getByLabel('Username or Email Address').fill('admin');
    await page.getByLabel('Password', { exact: true }).fill('password');
    await page.getByRole('button', { name: 'Log In' }).click();
    await page.getByRole('link', { name: 'DSubscribers', exact: true }).click();

    await expect(page.getByText(name)).toBeVisible()
})
