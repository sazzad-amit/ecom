import '../css/app.css'
import './bootstrap'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import clickOutside from './directives/clickOutside'
import { createPinia } from 'pinia'

// Make Quill globally available for vueup/vue-quill
import Quill from 'quill'
window.Quill = Quill

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,

  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    ),

  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(createPinia())
      .use(ZiggyVue)
      .directive('click-outside', clickOutside)
      .mount(el)
  },

  progress: { color: '#4B5563' },
})
