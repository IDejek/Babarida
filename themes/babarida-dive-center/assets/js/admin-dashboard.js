/**
 * Babarida Admin Dashboard JS
 *
 * @package Babarida_Dive_Center
 */

(function($) {
    'use strict';

    // Live clock
    function updateAdminClock() {
        var el = document.getElementById('adminClock');
        if (el) {
            el.textContent = new Date().toLocaleTimeString('en-GB', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }
    updateAdminClock();
    setInterval(updateAdminClock, 1000);

    // Confirm destructive actions
    $(document).on('change', 'select', function() {
        var val = $(this).val();
        if (val && val.indexOf('delete') !== -1) {
            if (!confirm('Are you sure you want to delete this item? This cannot be undone.')) {
                $(this).val('');
            }
        }
    });

    // AJAX form submissions
    $(document).on('submit', '.babarida-ajax-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        var originalText = btn.html();

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

        $.post(babaridaAdmin.ajaxUrl, form.serialize() + '&action=babarida_admin_ajax&nonce=' + babaridaAdmin.nonce, function(res) {
            if (res.success) {
                btn.html('<i class="fa-solid fa-check"></i> Saved').addClass('button-success');
                setTimeout(function() {
                    btn.prop('disabled', false).html(originalText).removeClass('button-success');
                }, 2000);
            } else {
                alert(res.data || 'Error occurred.');
                btn.prop('disabled', false).html(originalText);
            }
        }).fail(function() {
            alert('Network error.');
            btn.prop('disabled', false).html(originalText);
        });
    });

})(jQuery);
