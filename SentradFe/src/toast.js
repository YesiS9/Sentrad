// src/toast.js
import { useToast } from '@vue-toastification/vue3';
import 'vue-toastification/dist/index.css';

const toast = useToast({
  // You can set your default options here
  position: 'top-right',
  timeout: 5000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: 'button',
  icon: true,
  rtl: false,
});

export default toast;
