import {test, expect} from '@playwright/test'

const loginAdmin = async (page) => {
    await page.goto('/wp-admin');
    await page.getByLabel('Username or Email Address').fill('admin');
    await page.getByLabel('Password', {exact: true}).fill('password');
    await page.getByRole('button', {name: 'Log In'}).click();
    await page.getByRole('link', {name: 'DSubscribers', exact: true}).click();
}

const subscribe = async (page, name) => {
    await page.goto('/')
    await page.locator('#form-validation').getByPlaceholder('E-mail').fill(`${name}@example.com`);
    await page.locator('#form-validation').getByRole('button', {name: 'SUBMIT'}).click();
}

test('user subscribe', async ({page}) => {
    const name = crypto.randomUUID()
    await subscribe(page, name);

    await loginAdmin(page);

    await expect(page.getByText(name)).toBeVisible()
})

test('user unsubscribe', async ({page}) => {
    const name = crypto.randomUUID()
    await subscribe(page, name);

    await page.locator('#form-validation-unsubscribe').getByPlaceholder('E-mail').fill(`${name}@example.com`);
    await page.locator('#form-validation-unsubscribe').getByRole('button', {name: 'SUBMIT'}).click();

    await loginAdmin(page);

    await expect(page.getByText(name)).not.toBeVisible()
})
