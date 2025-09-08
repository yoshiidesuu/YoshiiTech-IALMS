import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import Bootstrap JavaScript
import 'bootstrap';

// Import Popper.js (required for Bootstrap dropdowns, popovers, tooltips)
import { createPopper } from '@popperjs/core';
window.createPopper = createPopper;

// Import and initialize Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
