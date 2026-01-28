import '../css/app.css'
import './bootstrap'
import 'primeicons/primeicons.css'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

import PrimeVue from 'primevue/config'

import { definePreset } from '@primeuix/themes'
import Aura from '@primeuix/themes/aura'
import HomeLayout from './Layouts/HomeLayout.vue'

const MyPreset = definePreset(Aura, {
  semantic: {
    primary: {
      50: '{sky.50}',
      100: '{sky.100}',
      200: '{sky.200}',
      300: '{sky.300}',
      400: '{sky.400}',
      500: '{sky.500}',
      600: '{sky.600}',
      700: '{sky.700}',
      800: '{sky.800}',
      900: '{sky.900}',
      950: '{sky.950}',
    },
  },
})

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => {
    return resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    ).then((module) => {
      module.default.layout ??= HomeLayout
      return module
    })
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

    app.use(PrimeVue, {
      theme: {
        preset: MyPreset,
        options: {
          darkModeSelector: '.my-app-dark',
        },
      },
    })

    app.use(plugin).use(ZiggyVue).mount(el)
  },
  progress: { color: '#4B5563' },
})
