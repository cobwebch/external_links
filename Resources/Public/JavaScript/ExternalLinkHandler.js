/*
 * This file is part of the Cobweb/ExternalLinks project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Module: Cobweb/ExternalLinks/ExternalLinkHandler
 */
define([
    'jquery',
    'TYPO3/CMS/Recordlist/LinkBrowser',
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/Backend/Severity',
    'Cobweb/ExternalLinks/LocalizationUtility',
    'TYPO3/CMS/Backend/Notification',
    'datatables'
], function($, LinkBrowser, Modal, Severity, LocalizationUtility, Notification) {
    'use strict';

    /**
     * @type {{}}
     * @exports Cobweb/ExternalLinks/ExternalLinkHandler
     */
    var ExternalLinkHandler;

    ExternalLinkHandler = {
        /**
         * Keep reference of the dataTable
         */
        dataTable: null,

        /**
         * Keep reference of the modal window
         */
        modal: null,

        /**
         * Keep reference of the current create form
         */
        $form: null,

        /**
         * Keep reference of the grid
         */
        $grid: null,

        /**
         * Initialize the View
         */
        initialize: function() {

            this.$form = $('#form-external-link-new');
            this.$grid = $('#table-external-links');

            ExternalLinkHandler.initializeGrid();
            ExternalLinkHandler.initializeSearch();
            ExternalLinkHandler.initializeEditButton();
            ExternalLinkHandler.initializeDeleteButton();
            ExternalLinkHandler.initializeSetLinkAndCloseWindow();
            ExternalLinkHandler.initializeNewForm();
        },

        /**
         * Initialize the Grid.
         */
        initializeGrid: function() {

            this.dataTable = this.$grid.DataTable({
                paging: false,
                info: false,
                language: {
                    url: "../typo3conf/ext/external_links/Resources/Public/JavaScript/Lang/datatables." + $('html').attr('lang') + ".json"
                },
                ajax: {
                    url: TYPO3.settings.ajaxUrls['external_link_action_list'],
                    data: function ( d ) {
                        d.search = ExternalLinkHandler.urlencode($('#table-external-links_filter').find('input[type="search"]').first().val())
                        // Retrieve current uid from title
                        var matches = $(".element-browser-title").clone().children().remove().end().text().match(/\(([^)]+)\)/);
                        d.uid = (matches != null ? matches[1] : 0);
                    }
                },
                "processing": true,
                columns: [
                    {data: 'icon'},
                    {data: 'label'},
                    {data: 'note'},
                    {data: 'commands'}
                ]
            });
        },

        /**
         * Initialize the "new" form
         * Action when the "new" form is submitted
         */
        initializeNewForm: function() {

            this.$form.on('submit', function(event) {
                event.preventDefault();

                // We first validate the form
                if (ExternalLinkHandler.validate('url')) {
                    $.ajax({
                        url: TYPO3.settings.ajaxUrls['external_link_action_create'],
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {

                            // Reload the Grid and act when done
                            ExternalLinkHandler.dataTable.ajax.reload(function () {

                                // Grid is not in a loading state anymore.
                                ExternalLinkHandler.setIsLoading(false);

                                // Reset form for a new link.
                                ExternalLinkHandler.$form.get(0).reset();

                                if (response && response.url) {
                                    Notification.success(LocalizationUtility.localize('message.success'), response.url);

                                } else {
                                    Notification.error(LocalizationUtility.localize('message.error'), '');
                                }
                            });
                        }
                    });
                }
            });
        },

        initializeSearch: function() {
            var self = this;

            $('#table-external-links_filter label').append('<a href="#" class="btn btn-default" id="ajax-reload">Search</a>');
            $('#ajax-reload').on('click', function() {
                console.log('button reload');
                ExternalLinkHandler.dataTable.ajax.reload(function() {
                    console.log('Finished reload from button');
                });
            });
            $('body').on('keypress', '#table-external-links_filter input[type="search"]', function() {
                console.log('Keypress reload');
                clearTimeout(self.timer);
                self.timer = setTimeout(ExternalLinkHandler.reloadSearch, 800);
            });
        },

        reloadSearch: function() {
            ExternalLinkHandler.dataTable.ajax.reload(function() {
                console.log('Finished reload from keypress');
            });
        },

        /**
         * Initialize the edit button
         * Action when a link is clicked => close the popup and inject the value into the field.
         */
        initializeSetLinkAndCloseWindow: function() {
            $(document).on('click', '.container-external-link-label a', function(e) {
                e.preventDefault();
                LinkBrowser.finalizeFunction('t3://externalLink?uid=' + $(this).data('uid'));
            })
        },

        /**
         * Initialize the edit button
         * Action when the edit button is clicked.
         */
        initializeEditButton: function() {

            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();

                var linkIdentifier = $(this).data('uid'),
                    variables = {
                        0: $(this).data('url'),
                        1: $(this).data('note'),
                        2: $(this).data('uid')
                    },
                    template = $('#template-external-link-edit').html();

                for (var key in variables) {
                    var regexp = new RegExp('%' + key + '|%s');
                    template = template.replace(regexp, variables[key]);
                }

                ExternalLinkHandler.modal = Modal.advanced(
                    {

                        title: LocalizationUtility.localize('modal.edit'),
                        content: template,
                        severity: Severity.notice,
                        buttons: [
                            {
                                text: LocalizationUtility.localize('action.cancel'),
                                btnClass: 'btn btn-default',
                                trigger: function() {
                                    Modal.dismiss();
                                }
                            },
                            {
                                text: LocalizationUtility.localize('action.update'),
                                btnClass: 'btn btn-primary',
                                trigger: function() {
                                    $('.form-external-link-edit', ExternalLinkHandler.modal).submit()
                                }
                            }
                        ],
                        callback: function() {
                            // Add a little time out to ensure the DOM is ready.
                            setTimeout(function() {
                                ExternalLinkHandler.initializeEditForm()
                            }, 0)
                        }
                    }
                )
            });
        },

        /**
         * Initialize the View
         * Action when the "edit" form is submitted
         */
        initializeEditForm: function() {

            $('.form-external-link-edit', ExternalLinkHandler.modal).on('submit', function(event) {
                event.preventDefault();

                // Grid is not in a loading state anymore.
                ExternalLinkHandler.setIsLoading(true);

                // Avoid double submit.
                $('.modal-dialog .btn-primary', ExternalLinkHandler.modal).addClass('disabled');

                $.ajax({
                    url: TYPO3.settings.ajaxUrls['external_link_action_update'],
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {

                        // Reload the Grid.
                        ExternalLinkHandler.dataTable.ajax.reload();

                        // Grid is not in a loading state anymore.
                        ExternalLinkHandler.setIsLoading(false);

                        // Close modal window
                        Modal.dismiss();
                    }
                });
            });
        },

        /**
         * Initialize the delete button
         * Action when the delete button is clicked.
         */
        initializeDeleteButton: function() {

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();

                var linkIdentifier = $(this).data('uid'),
                    linkUrl = $(this).data('url');

                Modal.confirm(
                    LocalizationUtility.localize('modal.delete'),
                    LocalizationUtility.localize('modal.delete.sure?', {0: linkUrl, 1: linkIdentifier}),
                    Severity.warning,
                    [
                        {
                            text: LocalizationUtility.localize('action.cancel'),
                            btnClass: 'btn btn-default',
                            trigger: function() {
                                Modal.dismiss();
                            }
                        },
                        {
                            text: LocalizationUtility.localize('action.delete'),
                            btnClass: 'btn btn-warning',
                            trigger: function() {

                                // Grid is not in a loading state anymore.
                                ExternalLinkHandler.setIsLoading(true);

                                $.ajax({
                                    url: TYPO3.settings.ajaxUrls['external_link_action_delete'],
                                    method: 'POST',
                                    data: {uid: linkIdentifier},
                                    success: function(response) {

                                        // Reload the Grid.
                                        ExternalLinkHandler.dataTable.ajax.reload();

                                        // Grid is not in a loading state anymore.
                                        ExternalLinkHandler.setIsLoading(false);

                                        // Close modal window
                                        Modal.dismiss();
                                    }
                                })
                            }
                        }
                    ]
                );
            });
        },

        /**
         * Set the Grid as loading state
         */
        setIsLoading: function(isLoading) {
            if (isLoading) {
                this.$form.find('input, button').attr('disabled', '');
                this.$grid.css({opacity: 0.4});
            } else {
                this.$form.find('input, button').removeAttr('disabled');
                this.$grid.css({opacity: 1});
            }
        },

        /**
         * Validate
         */
        validate: function(field) {
            hasError = true;
            if (field === 'url') {
                var $input, hasError;
                $input = $('#field-url');

                hasError = !/^(http(s?))\:\/\//.test($input.val());
                if (hasError) {
                    $input.parent('div').addClass('has-error');
                } else {
                    $input.parent('div').removeClass('has-error');
                }
            }
            return !hasError;
        },

        /**
         * JS Equivalence of PHP urlencode
         */
        urlencode: function (str) {
            str = (str + '')
            return encodeURIComponent(str)
                .replace(/!/g, '%21')
                .replace(/'/g, '%27')
                .replace(/\(/g, '%28')
                .replace(/\)/g, '%29')
                .replace(/\*/g, '%2A')
                .replace(/%20/g, '+')
        }
    };

    // After the DOM is loaded... time to react!
    $(function() {
        ExternalLinkHandler.initialize()
    });

    return ExternalLinkHandler;
});