import { createInertiaApp, Link, Head } from '@inertiajs/vue3';
import { createApp, h, type DefineComponent } from 'vue';
import { createI18n } from 'vue-i18n';
import { vReveal } from './directives/reveal';
import '../css/app.css';
import en from './locales/en.json';
import de from './locales/de.json';

const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: document.documentElement.lang.startsWith('de') ? 'de' : 'en',
    fallbackLocale: 'en',
    messages: { en, de },
    datetimeFormats: {
        en: { short: { year: 'numeric', month: 'short', day: 'numeric' } },
        de: { short: { year: 'numeric', month: 'short', day: 'numeric' } },
    },
});

createInertiaApp({
    title: (title) => (title ? `${title} — Octavia` : 'Octavia'),
    resolve: (name) => {
        const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue', { eager: true });
        return pages[`./pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        // TEMP-DIAG: surface silent prod render errors (remove after debugging)
        app.config.errorHandler = (err: unknown, _inst: unknown, info: string) => {
            console.error('VUE-ERR [' + info + ']:', err);
        };
        app.use(plugin);
        app.use(i18n);
        app.directive('reveal', vReveal);
        app.component('InertiaLink', Link);
        app.component('InertiaHead', Head);
        app.mount(el);
    },
    progress: {
        color: '#ea580c',
        showSpinner: false,
    },
});
