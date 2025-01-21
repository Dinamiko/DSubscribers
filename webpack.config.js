const defaultConfig = require('@wordpress/scripts/config/webpack.config')
const path = require('path')

module.exports = {
    ...defaultConfig,
    ...{
        entry: {
            ...defaultConfig.entry(),
            frontend: path.resolve(process.cwd(), 'assets/js', 'frontend.js'),
            "frontend-css": path.resolve(process.cwd(), 'assets/css', 'frontend.css'),
            admin: path.resolve(process.cwd(), 'assets/js/admin', 'settings.js'),
            "admin-css": path.resolve(process.cwd(), 'assets/css/admin', 'settings.scss'),
            subscribers: path.resolve(process.cwd(), 'assets/js/admin/subscribers', 'subscribers.js'),
            "subscribers-css": path.resolve(process.cwd(), 'assets/css/admin/subscribers', 'subscribers.scss'),
        }
    }
}
