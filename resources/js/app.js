import '../css/app.css';
import './bootstrap';
import 'primeicons/primeicons.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import PrimeVue from 'primevue/config'; 
import '../assets/base.css'; 

import Aura from '@primeuix/themes/aura';
import HomeLayout from './Layouts/HomeLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
    return resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')
        ).then((module) => {
        // ✅ nastav default layout, iba ak stránka nemá vlastný
            module.default.layout ??= HomeLayout
            return module
        })
    },
    setup({ el, App, props, plugin }) {
       
        const app = createApp({
            render: () => h(App, props)
        });

        app.use(PrimeVue, {
            // Default theme configuration
            theme: {
                preset: Aura,
                options: {
                    darkModeSelector: '.my-app-dark',
                }
            }
         });

        app.use(plugin)
           .use(ZiggyVue)
           .mount(el);
    },
    progress: {
        color: '#4B5563', 
    },
});
