import "./bootstrap";
import "../css/app.css";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";
import VueApexCharts from "vue3-apexcharts";
import helper from "./mixins/layouts.mixin";
import Vuelidate from "vuelidate";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

import Vue3Signature from "vue3-signature";
import "sweetalert2/src/sweetalert2.scss";

import i18n from "./i18n";
import { BootstrapVueNext } from "bootstrap-vue-next";

// import 'bootstrap/dist/css/bootstrap.css'
import "bootstrap-vue-next/dist/bootstrap-vue-next.css";
import "leaflet/dist/leaflet.css";

//importing the vuex store
import store from "./state/store.js";

import Vueform from "@vueform/vueform";
import vueformConfig from "../../vueform.config";
// state management
import { createPinia } from "pinia";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import { Loader } from "@googlemaps/js-api-loader";

//WYSIWYG editor
import CKEditor from "@ckeditor/ckeditor5-vue";

const appName = import.meta.env.VITE_APP_NAME || "Care Link";
const bscURL = "water";

import "ant-design-vue/dist/reset.css";
import Antd from "ant-design-vue";



createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue")),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        //registering mixins
        app.mixin(helper);

        //global properties
        app.config.globalProperties.bscURL = bscURL;

        const options = {};

        return app
            .use(Vueform, vueformConfig)
            .use(createPinia())
            .use(Toast, options)
            .use(plugin)
            .use(i18n)
            .use(store)
            .use(Antd)
            .use(ZiggyVue)
            .use(Vuelidate)
            .use(vSelect)
            .use(Vue3Signature)
            .use(VueApexCharts)
            .use(BootstrapVueNext)
            .use(CKEditor)
            .use(bscURL)
            .mount(el);
    },
    progress: {
        color: "#6bbaff",
    },
});
