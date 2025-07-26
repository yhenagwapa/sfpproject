import './bootstrap';
import Alpine from 'alpinejs';
import $ from 'jquery';
window.$ = window.jQuery = $;
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

window.Alpine = Alpine;
Alpine.start();

flatpickr(".date-field", {
  dateFormat: "m-d-Y",
  allowInput: false
});

import './main';
