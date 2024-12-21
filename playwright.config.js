import {defineConfig} from '@playwright/test'

export default defineConfig({
    testDir: 'tests/acceptance',
    use: {
        baseURL: 'http://localhost:8888'
    }
})
