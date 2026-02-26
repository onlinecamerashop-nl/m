require(["jquery"], function ($) {
    "use strict";

    var initialized = {};

    // Triggered by frontend_model when data-image is ready
    $(document).on('selectImageDataReady', function (e, fieldId) {
        initializeField(fieldId);
    });

    // Fallback: auto-init any select-image that already has data-image
    $(document).on('adminSystemConfig', function () {
        $('.select-image').each(function () {
            var id = $(this).attr('id');
            if ($('#' + id + ' option[data-image]').length && !initialized[id]) {
                initializeField(id);
            }
        });
    });

    function initializeField(id) {
        if (initialized[id]) return;
        initialized[id] = true;

        var $select = $('#' + id);
        if (!$select.length) return;

        var $preview = $('<div class="select-image-preview"></div>');
        $select.after($preview);

        // Build image items
        $select.find('option').each(function () {
            var $opt = $(this);
            var val = $opt.val();
            var label = $opt.text().trim();
            var img = $opt.attr('data-image');

            if (val && img) {
                var $item = $(`
                    <div class="select-image-item" data-value="${val}">
                        <img src="${img}" alt="${label}" class="select-image-thumb" />
                        <div class="select-image-label">${label}</div>
                    </div>
                `);
                $preview.append($item);
            }
        });

        // Click to select
        $preview.on('click', '.select-image-item', function () {
            var value = $(this).data('value');
            // Remove active from all
            $preview.find('.select-image-item').removeClass('active');
            // Add active
            $(this).addClass('active');
            // Update select
            $select.val(value).trigger('change');
        });

        // Update active state on change
        function updateActive() {
            var currentValue = $select.val();
            $preview.find('.select-image-item')
                .removeClass('active')
                .filter('[data-value="' + currentValue + '"]')
                .addClass('active');
        }

        $select.on('change', updateActive);
        updateActive(); // Initial state
    }
});