import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const app = createApp(App);
app.use(router);
app.use(Toast, {
  // Optional options here
});

createApp(App).use(router).mount('#app')
