import Alpine from 'alpinejs';
import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';
import 'semantic-ui-transition/transition.min.css';
import 'semantic-ui-transition/transition.min.js';
import 'semantic-ui-dropdown/dropdown.min.css';
import 'semantic-ui-dropdown/dropdown.min.js';
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { Indonesian } from "flatpickr/dist/l10n/id.js";
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;
window.driver = driver;
window.flatpickr = flatpickr;
window.Swal = Swal;
flatpickr.localize(Indonesian);

// Global helper to initialize Flatpickr on date inputs
window.initFlatpickr = function(container) {
    if (typeof flatpickr === 'undefined') return;
    const scope = container || document;

    const setupAltInput = function(instance) {
        if (instance.altInput && instance.input && instance.input.id) {
            const origId = instance.input.id;
            instance.input.removeAttribute('id');
            instance.input.setAttribute('data-orig-id', origId);
            instance.altInput.id = origId;
        }
    };

    // Form Transaksi: Format tanggal transaksi
    scope.querySelectorAll('.datepicker-tx').forEach(function(el) {
        if (el._flatpickr) return;
        el.setAttribute('data-fp-hydrated', 'true');
        flatpickr(el, {
            locale: 'id',
            altInput: true,
            altFormat: 'j F Y',
            dateFormat: 'Y-m-d',
            maxDate: el.getAttribute('max') || 'today',
            disableMobile: true,
            altInputClass: el.className.replace(/datepicker(-\w+)?/g, '').trim() + ' cursor-pointer',
            onReady: function(selectedDates, dateStr, instance) {
                setupAltInput(instance);
            }
        });
    });

    // Laporan & Filter: Format tanggal bulan teks
    scope.querySelectorAll('.datepicker-report').forEach(function(el) {
        if (el._flatpickr) return;
        el.setAttribute('data-fp-hydrated', 'true');
        flatpickr(el, {
            locale: 'id',
            altInput: true,
            altFormat: 'j F Y',
            dateFormat: 'Y-m-d',
            maxDate: el.getAttribute('max') || 'today',
            disableMobile: true,
            altInputClass: el.className.replace(/datepicker(-\w+)?/g, '').trim() + ' cursor-pointer',
            onReady: function(selectedDates, dateStr, instance) {
                setupAltInput(instance);
            }
        });
    });

    // Generic Datepickers & input[type="date"]
    scope.querySelectorAll('.datepicker, input[type="date"]').forEach(function(el) {
        if (el._flatpickr) return;
        el.setAttribute('data-fp-hydrated', 'true');
        flatpickr(el, {
            locale: 'id',
            altInput: true,
            altFormat: 'j F Y',
            dateFormat: 'Y-m-d',
            maxDate: el.getAttribute('max') || undefined,
            disableMobile: true,
            altInputClass: el.className.replace(/datepicker(-\w+)?/g, '').trim() + ' cursor-pointer',
            onReady: function(selectedDates, dateStr, instance) {
                setupAltInput(instance);
            }
        });
    });
};

// Global helper to initialize Semantic UI dropdowns with Alpine.js & form event compatibility
window.initSemanticDropdowns = function(container) {
    if (typeof jQuery === 'undefined' || !jQuery.fn || !jQuery.fn.dropdown) return;
    const $target = container ? jQuery(container).find('.ui.dropdown') : jQuery('.ui.dropdown');
    $target.each(function() {
        const $el = jQuery(this);
        if ($el.data('module-dropdown') || $el.hasClass('hydrated-semantic')) return;
        $el.addClass('hydrated-semantic');

        $el.dropdown({
            fullTextSearch: true,
            forceSelection: false,
            selectOnKeydown: false,
            onChange: function(value, text, $choice) {
                const rawEl = this.tagName === 'SELECT' ? this : this.querySelector('select, input[type="hidden"]');
                if (rawEl) {
                    rawEl.dispatchEvent(new Event('change', { bubbles: true }));
                    rawEl.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        });
    });
};

document.addEventListener('DOMContentLoaded', function() {
    window.initFlatpickr();
    window.initSemanticDropdowns();
});

document.addEventListener('alpine:initialized', function() {
    window.initFlatpickr();
    window.initSemanticDropdowns();
});

if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver(() => {
        if (typeof jQuery !== 'undefined' && jQuery.fn && jQuery.fn.dropdown) {
            const $uninitialized = jQuery('.ui.dropdown:not(.hydrated-semantic)');
            if ($uninitialized.length) {
                window.initSemanticDropdowns();
            }
        }
        if (typeof flatpickr !== 'undefined') {
            const uninitializedDates = document.querySelectorAll('.datepicker-tx:not([data-fp-hydrated]), .datepicker-report:not([data-fp-hydrated])');
            if (uninitializedDates.length) {
                window.initFlatpickr();
            }
        }
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });
}

Alpine.start();
