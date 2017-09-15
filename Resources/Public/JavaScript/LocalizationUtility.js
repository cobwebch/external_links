/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Module: Cobweb/ExternalLinks/LocalizationUtility
 */
define([], function() {
    'use strict';

    var lang = ExternalLinks.lang;

    /**
     * @type {{localize: localize}}
     */
    return {

        /**
         * @param {string} label
         * @param {string} replace
         * @param {string} plural
         * @returns {string}
         */
        localize: function(label, replace, plural) {
            if (typeof lang === 'undefined' || typeof lang[label] === 'undefined') {
                return false;
            }

            var i = plural || 0,
                translationUnit = lang[label],
                label = null,
                regexp = null;

            // Get localized label
            if (typeof translationUnit === 'string') {
                label = translationUnit;
            } else {
                label = translationUnit[i]['target'];
            }

            // Replace
            if (typeof replace !== 'undefined') {
                for (var key in replace) {
                    regexp = new RegExp('%' + key + '|%s');
                    label = label.replace(regexp, replace[key]);
                }
            }

            return label;
        }
    };
});