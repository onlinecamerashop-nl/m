define([
    'rjsResolver'
], function (resolver) {
    'use strict';

    return function (target) {
        return function (config, $loader) {
            // Original behavior: Remove loader element when assets are resolved
            resolver(function () {
                if ($loader && $loader.parentNode) {
                    $loader.parentNode.removeChild($loader);
                }
            });

            // Hide skeleton loader
            document.querySelectorAll('.opc-wrapper.skeleton, .opc-sidebar.skeleton').forEach(element => {
                element.style.display = 'none';
            });
        };
    };
});