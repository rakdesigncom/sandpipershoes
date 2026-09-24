define([
    'jquery',
    'underscore',
], function ($, _) {
    'use strict';

    var widgetMixin = {
        _RenderSwatchOptions: function (config, controlId) {
            if (config.code === 'sizes') {
                config.options.sort((a, b) => {
                    const numA = parseInt(a.label ? a.label?.split('-')[0] : 0, 10);
                    const numB = parseInt(b.label ? b.label?.split('-')[0] : 0, 10);
                    return numA - numB;
                });
            } else {
                config.options.sort((a, b) => a.label?.localeCompare(b.label));
            }

            return this._super(config, controlId);
        }
    };

    return function (targetWidget) {
        $.widget('mage.SwatchRenderer', targetWidget, widgetMixin);

        return $.mage.SwatchRenderer;
    };
});
