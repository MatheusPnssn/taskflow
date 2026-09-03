import '../css/app.css'
import 'bootstrap'

import axios from 'axios'
window.axios = axios;

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import NProgress from 'nprogress'

import 'nprogress/nprogress.css'

NProgress.configure({
    showSpinner: false,
})

const appElement = document.getElementById('app');
const initialPageData = JSON.parse(appElement.dataset.page);

createInertiaApp({
    page: initialPageData,

    resolve: (name) => {
        const pages = import.meta.glob('../views/pages/**/*.vue', { eager: true });
        const pageKey = `../views/pages/${name}.vue`;

        if (!pages[pageKey]) {
            throw new Error(`Componente "${name}" não encontrado.`);
        }
        return pages[pageKey].default || pages[pageKey];
    },

    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h(App, props),
        })
            .use(plugin)
            .mount(el);
    },
});