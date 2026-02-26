/**
 * Copyright © MgtWizards. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
define(["jquery", "mage/translate"], function ($, $t) {
    $.widget("mgtwizards.regenerateProducts", {
        options: {
            action_url: null,
        },

        _create: function () {
            var self = this;
            this.element.on("click", function () {
                self.element.addClass("disabled");
                self.runRegenerateProducts();
            });
        },

        runRegenerateProducts: function () {
            var params = { "regenerate-products": true };
            var self = this;
            $.ajax({
                url: self.options.action_url,
                data: params,
                method: "POST",
                dataType: "json",
                showLoader: true,
                context: this,
            })
                .success(function (response) {
                    var validationMessage = $(".validation-result");
                    var text = '<ul style="list-style-type: none">';
                    response.each(function (value) {
                        if (value.type == "success") {
                            text += '<li class="message message-success success">' + $t(value.message) + "</li>";
                            validationMessage.removeClass("hidden message-error error");
                        } else if (value.type == "error") {
                            text += '<li class="message message-error error">' + $t(value.message) + "</li>";
                            validationMessage.removeClass("hidden message-success success");
                        }
                    });
                    text += "</ul>";
                    validationMessage.html(text);
                    return false;
                })
                .error(function (xhr) {
                    if (xhr.statusText === "abort") {
                        return;
                    }
                    alert($t("Something went wrong."));
                })
                .always(function () {
                    self.element.removeClass("disabled");
                });
        },
    });

    return $.mgtwizards.regenerateProducts;
});
