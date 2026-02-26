const { postcssImportHyvaModules } = require("@hyva-themes/hyva-modules");

module.exports = {
    plugins: [
        postcssImportHyvaModules({
            excludeDirs: [
                "vendor/hyva-themes/magento2-hyva-checkout/src",
                "vendor/hyva-themes/magento2-magezon-builder/src",
            ],
        }),
        require('postcss-import'),
        require('tailwindcss/nesting'),
        require('tailwindcss'),
        require('postcss-preset-env'),
    ]
}
