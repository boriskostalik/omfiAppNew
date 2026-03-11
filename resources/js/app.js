import '../css/app.css'
import './bootstrap'
import 'primeicons/primeicons.css'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

import PrimeVue from 'primevue/config'

import { definePreset } from '@primeuix/themes'
import Material from '@primeuix/themes/material'
import HomeLayout from './Layouts/HomeLayout.vue'

const MyPreset = definePreset(Material, {
  primitive: {
    omfiBlue: {
      50:  '#f0f4fb',
      100: '#d9e5f5',
      200: '#a8c5e8',
      300: '#68a0d5',
      400: '#2c70be',
      500: '#1E4E8C',
      600: '#184073',
      700: '#13335c',
      800: '#0e2544',
      900: '#09182c',
      950: '#05101c',
    },
    omfiRed: {
      50:  '#fef2f2',
      100: '#fee2e2',
      200: '#fecaca',
      300: '#fca5a5',
      400: '#f87171',
      500: '#ef4444',
      600: '#dc2626',
      700: '#b91c1c',
      800: '#991b1b',
      900: '#7f1d1d',
      950: '#450a0a',
    },
    omfiBlack: {
      50:  '#f5f5f5',
      100: '#e5e5e5',
      200: '#c5c5c5',
      300: '#a5a5a5',
      400: '#757575',
      500: '#454545',
      600: '#252525',
      700: '#151515',
      800: '#0d0d0d',
      900: '#080808',
      950: '#030303',
    },
    omfiGreen: {
      50:  '#f0fdf4',
      100: '#dcfce7',
      200: '#bbf7d0',
      300: '#86efac',
      400: '#4ade80',
      500: '#22c55e',
      600: '#16a34a',
      700: '#15803d',
      800: '#166534',
      900: '#14532d',
      950: '#052e16',
    },
  },
  semantic: {
    primary: {
      50:  '{omfiBlue.50}',
      100: '{omfiBlue.100}',
      200: '{omfiBlue.200}',
      300: '{omfiBlue.300}',
      400: '{omfiBlue.400}',
      500: '{omfiBlue.500}',
      600: '{omfiBlue.600}',
      700: '{omfiBlue.700}',
      800: '{omfiBlue.800}',
      900: '{omfiBlue.900}',
      950: '{omfiBlue.950}',
    },
    green: {
      50:  '{omfiGreen.50}',
      100: '{omfiGreen.100}',
      200: '{omfiGreen.200}',
      300: '{omfiGreen.300}',
      400: '{omfiGreen.400}',
      500: '{omfiGreen.500}',
      600: '{omfiGreen.600}',
      700: '{omfiGreen.700}',
      800: '{omfiGreen.800}',
      900: '{omfiGreen.900}',
      950: '{omfiGreen.950}',
    },
    red: {
      50:  '{omfiRed.50}',
      100: '{omfiRed.100}',
      200: '{omfiRed.200}',
      300: '{omfiRed.300}',
      400: '{omfiRed.400}',
      500: '{omfiRed.500}',
      600: '{omfiRed.600}',
      700: '{omfiRed.700}',
      800: '{omfiRed.800}',
      900: '{omfiRed.900}',
      950: '{omfiRed.950}',
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
          cssLayer: {
            name: 'primevue',
            order: 'tailwind-base, primevue, tailwind-utilities',
          },
        },
      },
    })

    app.use(plugin).use(ZiggyVue).mount(el)
  },
  progress: { color: '#4B5563' },
})
