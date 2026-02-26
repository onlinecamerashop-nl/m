/*
 * @author MgtWizards Team
 * @copyright Copyright (c) MgtWizards (https://shopwhizzy.com/)
 */
define([
    'underscore'
], function (_) {
    return function (Component) {
        return Component.extend({
            nullableFields: ['label_image'],
            /**
             * Forces empty arrays to be sent in data
             *
             * @param options
             * @returns {save|*}
             */
            save: function (options) {
                if (this.ns !== 'mgtwizards_labels_form') {
                    return this._super(options);
                }
                var data = this.get('data');
                _.each(this.nullableFields, function (field) {
                    if (_.isArray(data[field]) && !data[field].length) {
                        data[field] = '';
                    }
                });
                this.client.save(data, options);
                return this;
            }
        });
    }
});
