var Application = function () {
    var initialized = false;

    return {
        initialize: function()
        {
            // Some stuff here
            jQuery(document).ready( function() {
                Application.tooltipsInitialize();
                Application.timeAgoInitialize();
                Application.paginatorInitialize();
                Application.postMetasInitialize();
                Application.listActionsInitialize();
                Application.selectsInitialize();
                Application.passwordMetersInitialize();

                jQuery('#preloader').fadeOut(); // Hide preloader, when everything is ready...

                initialized = true;
                console.log('Application Initialized');
            });
        },
        tooltipsInitialize: function() {
            jQuery('[data-toggle="tooltip"]').tooltip();
        },
        timeAgoInitialize: function() {
            function updateTime() {
                var now = moment();

                jQuery('time.time-ago').each( function() {
                    var time = moment(jQuery(this).attr('datetime'));

                    jQuery(this).text(time.from(now));
                });
            }

            updateTime();

            setInterval(updateTime, 10000);
        },
        paginatorInitialize: function() {
            var currentUrl = window.location.href;
            var limitPerPageParameter = 'limit_per_page';
            var pageParameter = 'page';
            var searchParameter = 'search';
            var url = new URI(currentUrl);

            if (jQuery('#paginator-limit-per-page-select').length) {
                jQuery('#paginator-limit-per-page-select').on('change', function() {
                    var value = jQuery(this).val();

                    url.removeQuery(limitPerPageParameter);
                    url.addQuery(limitPerPageParameter, value);

                    url.removeQuery(pageParameter);
                    url.addQuery(pageParameter, 1);

                    window.location.href = url.toString();
                });
            }

            if (jQuery('#paginator-search-input').length) {
                jQuery('#paginator-search-button').on('click', function() {
                    var value = jQuery('#paginator-search-input').val();

                    url.removeQuery(searchParameter);
                    url.addQuery(searchParameter, value);

                    url.removeQuery(pageParameter);
                    url.addQuery(pageParameter, 1);

                    window.location.href = url.toString();
                });

                jQuery('#paginator-search-clear-button').on('click', function() {
                    var value = '';

                    url.removeQuery(searchParameter);

                    window.location.href = url.toString();
                });
            }
        },
        postMetasInitialize: function() {
            var postMetasCount = jQuery('#postMetas-fields-list li').length;
            
            jQuery('#new-post-meta').on('click', function(e) {
                e.preventDefault();
                var postMetas = jQuery('#postMetas-fields-list');
                var newWidget = postMetas.attr('data-prototype');
                newWidget = newWidget.replace(/__name__/g, postMetasCount);
                postMetasCount++;
                var newLi = jQuery('<li></li>').html(
                    newWidget+
                    '<div class="clearfix">' +
                        '<div class="pull-right">' +
                            '<a class="btn btn-xs btn-danger remove-post-meta-button"' +
                                'href="#">' +
                                '<i class="fa fa-times"></i>' +
                            '</a>' +
                        '</div>' +
                    '</div>'
                );
                newLi.appendTo(postMetas);
                initializeRemovePostMetaButton();
            });
            
            function initializeRemovePostMetaButton() {
                jQuery('.remove-post-meta-button').on('click', function(e) {
                    e.preventDefault();
                    jQuery(this).closest('li').remove();
                    postMetasCount--;
                });
            }
            initializeRemovePostMetaButton();
        },
        listActionsInitialize: function() {
            jQuery('#check-all-checkbox').on('click', function() {
                var isChecked = jQuery(this).is(':checked');

                if(isChecked) {
                    jQuery('.object-checkbox').prop('checked', true);
                } else {
                    jQuery('.object-checkbox').prop('checked', false);
                }
            });
            
            jQuery('#remove-selected-button').on('click', function() {
                var hasIds = false;
                var ids = [];

                jQuery('.object-checkbox').each(function() {
                    var isChecked = jQuery(this).is(':checked');

                    if(isChecked) {
                        hasIds = true;

                        ids.push(jQuery(this).attr('value'));
                    }
                });

                if(hasIds) {
                    window.location.href = jQuery(this).attr('href')+'?ids='+ids.join(',');
                } else {
                    alert('Please select at least one row!');
                }

                return false;
            });
            
            jQuery('.object-checkbox').on('click', function() {
                var all = jQuery('.object-checkbox').length;
                var checked = jQuery('.object-checkbox:checked').length;

                if (all == checked) {
                    jQuery('#check-all-checkbox').prop('checked', true);
                    jQuery('#check-all-checkbox').prop('indeterminate', false);
                } else if(checked == 0) {
                    jQuery('#check-all-checkbox').prop('checked', false);
                    jQuery('#check-all-checkbox').prop('indeterminate', false);
                } else {
                    jQuery('#check-all-checkbox').prop('checked', false);
                    jQuery('#check-all-checkbox').prop('indeterminate', true);
                }
            });
        },
        selectsInitialize: function() {
            jQuery('.select-picker').selectpicker();
        },
        passwordMetersInitialize: function() {
            var passwordMeterInput = jQuery('.password-meter-input');

            passwordMeterInput.each(function() {
                var self = jQuery(this);
                var passwordMeter = self.parent().find('.password-meter');

                self.on('keyup', function() {
                    var value = self.val();
                    var result = zxcvbn(value);
                    var score = result.score;
                    
                    passwordMeter.val(score);
                });
            });
        },
    }
}();
