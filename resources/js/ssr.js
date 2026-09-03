import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { createSSRApp, h } from 'vue'
import { renderToString } from 'vue/server-renderer'

import axios from 'axios'
if (typeof window !== 'undefined') {
    window.axios = axios;
}

createServer(page =>
    createInertiaApp({
        page,
        render: renderToString,
        resolve: name => {
            const pages = import.meta.glob('../views/pages/**/*.vue', { eager: true })
            const pageModule = pages[`../views/pages/${name}.vue`]
            return pageModule.default || pageModule
        },
        setup({ App, props, plugin }) {
            return createSSRApp({
                render: () => h(App, props),
            }).use(plugin)
        },
    }),
)