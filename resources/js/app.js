import './bootstrap';
import Alpine from 'alpinejs';

import $ from 'jquery';
window.$ = window.jQuery = $;
import 'bootstrap/dist/css/bootstrap.min.css';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

window.Alpine = Alpine;
Alpine.start();

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".date-field").forEach(input => {
        flatpickr(input, {
            dateFormat: "m-d-Y",
            minDate: input.getAttribute("min"),
            maxDate: input.getAttribute("max"),
            allowInput: false
        });
    });
});

import './main';
