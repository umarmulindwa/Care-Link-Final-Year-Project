// vueform.config.js

import en from '@vueform/vueform/locales/en';
// import tailwind from '@vueform/vueform/themes/tailwind';
import bootstrap from '@vueform/vueform/themes/bootstrap';
// import vueform from '@vueform/vueform/themes/vueform';

// You might place these anywhere else in your project
import '@vueform/vueform/themes/vueform/css/index.min.css';


export default {
  theme: bootstrap,
  locales: { en },
  locale: 'en',
}
