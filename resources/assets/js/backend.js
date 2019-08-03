
require('./bootstrap');

window.Vue = require('vue');
window.Slug = require('slug');
window.CKEditor = require('@ckeditor/ckeditor5-vue');
// window.ClassicEditor = require('@ckeditor/ckeditor5-build-classic');
// window.FullEditor = require('./components/ckeditor/ckeditor5-build-full'); 
window.FullEditor = require('@ckeditor/ckeditor5-build-full'); 
FullEditor.embedImageExecuted = false; 

Slug.defaults.mode = 'rfc3986';

window.baseUrl = location.protocol + "//" + location.hostname + (location.port && ":" + location.port);
window.origImageUrl = '/storage/content/original/';
window.thumb360ImageUrl = '/storage/content/thumb-360/';

import Buefy from 'buefy'
Vue.use(Buefy);

// import CKEditor from '@ckeditor/ckeditor5-vue';
// import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

Vue.use( CKEditor );
// Vue.use( ClassicEditor );

import ImageUploader from 'vue-image-upload-resize'
Vue.use(ImageUploader);

Vue.component('slug-widget', require('./components/slugWidget.vue').default);
Vue.component('slug-widget-simple', require('./components/slugWidget-simple.vue').default);
Vue.component('media-manager', require('./components/media-manager.vue').default);
Vue.component('image-preview', require('./components/image-preview.vue').default);

require('./tools')
