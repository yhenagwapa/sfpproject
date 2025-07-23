import './bootstrap';
import Alpine from 'alpinejs';
import $ from 'jquery';
window.$ = window.jQuery = $;
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

window.Alpine = Alpine;
Alpine.start();

import './main';
