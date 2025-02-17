import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import PrimeVue from 'primevue/config'; // Import PrimeVue
import '../assets/base.css'; // Importovanie base.css so základnými premennými

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        // Vytvorenie aplikácie Vue
        const app = createApp({
            render: () => h(App, props)
        });

        // Použitie PrimeVue s nastavením theme: 'none' bez ovplyvnenia ostatných pluginov
        app.use(PrimeVue, {
            theme: 'none', // Nastavenie témy na 'none'
        });

        // Použitie Inertia pluginu a ZiggyVue pluginu
        app.use(plugin)
           .use(ZiggyVue)
           .mount(el);
    },
    progress: {
        color: '#4B5563', // Nastavenie farby progresu
    },
});
