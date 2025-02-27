(function($) {
    'use strict';
    $(document).ready(function() {
        // tiptip js implementation
        jQuery( '.woocommerce-help-tip' ).tipTip( {
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200,
            'keepAlive': true
        } );

        //Activate plugin name in admin menu
        $('a[href="admin.php?page=wcdg-general-setting"]').parents().addClass('current wp-has-current-submenu');
        $('a[href="admin.php?page=wcdg-general-setting"]').addClass('current');

        /** Dynamic Promotional Bar START */
        $(document).on('click', '.dpbpop-close', function () {
            var popupName       = $(this).attr('data-popup-name');
            setCookie( 'banner_' + popupName, 'yes', 60 * 24 * 7);
            $('.' + popupName).hide();
        });

        $(document).on('click', '.dpb-popup .dpb-popup-meta a', function () {
            var promotional_id = $(this).parents().find('.dpbpop-close').attr('data-bar-id');

            // Create a new Student object using the values from the textfields
            var apiData = {
                'bar_id' : promotional_id
            };

            $.ajax({
                type: 'POST',
                url: admin_basic_vars.dpb_api_url + 'wp-content/plugins/dots-dynamic-promotional-banner/bar-response.php',
                data: JSON.stringify(apiData), // now data come in this function
                dataType: 'json',
                cors: true,
                contentType:'application/json',
                
                success: function (data) {
                    console.log(data);
                },
                error: function () {
                }
             });
        });
        /** Dynamic Promotional Bar END */

        /** Plugin Setup Wizard Script START */
        // Hide & show wizard steps based on the url params 
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('require_license')) {
            $('.ds-plugin-setup-wizard-main .tab-panel').hide();
            $( '.ds-plugin-setup-wizard-main #step5' ).show();
        } else {
            $( '.ds-plugin-setup-wizard-main #step1' ).show();
        }
        
        // Plugin setup wizard steps script
        $(document).on('click', '.ds-plugin-setup-wizard-main .tab-panel .btn-primary:not(.ds-wizard-complete)', function () {
            var curruntStep = jQuery(this).closest('.tab-panel').attr('id');
            var nextStep = 'step' + ( parseInt( curruntStep.slice(4,5) ) + 1 ); // Masteringjs.io

            if( 'step5' !== curruntStep ) {
                // Youtube videos stop on next step
                $('iframe[src*="https://www.youtube.com/embed/"]').each(function(){
                   $(this).attr('src', $(this).attr('src'));
                   return false;
                });
                
                jQuery( '#' + curruntStep ).hide();
                jQuery( '#' + nextStep ).show();   
            }
        });

        // Get allow for marketing or not
        if ( $( '.ds-plugin-setup-wizard-main .ds_count_me_in' ).is( ':checked' ) ) {
            $('#fs_marketing_optin input[name="allow-marketing"][value="true"]').prop('checked', true);
        } else {
            $('#fs_marketing_optin input[name="allow-marketing"][value="false"]').prop('checked', true);
        }

        // Get allow for marketing or not on change     
        $(document).on( 'change', '.ds-plugin-setup-wizard-main .ds_count_me_in', function() {
            if ( this.checked ) {
                $('#fs_marketing_optin input[name="allow-marketing"][value="true"]').prop('checked', true);
            } else {
                $('#fs_marketing_optin input[name="allow-marketing"][value="false"]').prop('checked', true);
            }
        });

        // Complete setup wizard
        $(document).on( 'click', '.ds-plugin-setup-wizard-main .tab-panel .ds-wizard-complete', function() {
            if ( $( '.ds-plugin-setup-wizard-main .ds_count_me_in' ).is( ':checked' ) ) {
                $( '.fs-actions button'  ).trigger('click');
            } else {
                $('.fs-actions #skip_activation')[0].click();
            }
        });

        //Checkbox functionality
        $('.check-column input[type="checkbox"]').on('click', function(){
            var checkAll = $(this).prop('checked');
            $('.td_select input[type="checkbox"]').prop('checked', checkAll);
        });

        // Send setup wizard data on Ajax callback
        $(document).on( 'click', '.ds-plugin-setup-wizard-main .fs-actions button', function() {
            var wizardData = {
                'action': 'wcdg_plugin_setup_wizard_submit',
                'survey_list': $('.ds-plugin-setup-wizard-main .ds-wizard-where-hear-select').val(),
                'nonce': admin_basic_vars.setup_wizard_ajax_nonce
            };

            $.ajax({
                url: admin_basic_vars.ajaxurl,
                data: wizardData,
                success: function ( success ) {
                    console.log(success);
                }
            });
        });
        /** Plugin Setup Wizard Script End */

        // Script for Beacon configuration
        var helpBeaconCookie = getCookie( 'wcdg-help-beacon-hide' );
        if ( ! helpBeaconCookie ) {
            if ( typeof Beacon === 'function' ) {
                Beacon('init', 'afe1c188-3c3b-4c5f-9dbd-87329301c920');
                Beacon('config', {
                    display: {
                        style: 'icon',
                        iconImage: 'message',
                        zIndex: '99999'
                    }
                });

                // Add plugin articles IDs to display in beacon
                Beacon('suggest', ['5dee43e104286364bc92a259', '5df9f36704286364bc92fe5f', '5dfa04c704286364bc92ff01', '5df9fa1104286364bc92fe97', '5dfb445104286364bc930ba3']);

                // Add custom close icon form beacon
                setTimeout(function() {
                    if ( $( '.hsds-beacon .BeaconFabButtonFrame' ).length > 0 ) {
                        let newElement = document.createElement('span');
                        newElement.classList.add('dashicons', 'dashicons-no-alt', 'dots-beacon-close');
                        let container = document.getElementsByClassName('BeaconFabButtonFrame');
                        container[0].appendChild( newElement );
                    }
                }, 3000);

                // Hide beacon
                $(document).on('click', '.dots-beacon-close', function(){
                    Beacon('destroy');
                    setCookie( 'wcdg-help-beacon-hide' , 'true', 24 * 60 );
                });
            }
        }

        /** Script for Freemius upgrade popup */
        $(document).on('click', '#dotsstoremain .wcdg-pro-label, .wcdg-upgrade-pro-to-unlock', function(){
            $('body').addClass('wcdg-modal-visible');
        });
        $(document).on('click', '.upgrade-to-pro-modal-main .modal-close-btn', function(){
            $('body').removeClass('wcdg-modal-visible');
        });
        $(document).on('click', '.dots-header .dots-upgrade-btn, .dotstore-upgrade-dashboard .upgrade-now', function(e){
            e.preventDefault();
            upgradeToProFreemius( '' );
        });
        $(document).on('click', '.upgrade-to-pro-modal-main .upgrade-now', function(e){
            e.preventDefault();
            $('body').removeClass('wcdg-modal-visible');
            let couponCode = $('.upgrade-to-pro-discount-code').val();
            upgradeToProFreemius( couponCode );
        });

        /** Upgrade Dashboard Script START */
        // Dashboard features popup script
        $(document).on('click', '.dotstore-upgrade-dashboard .premium-key-fetures .premium-feature-popup', function (event) {
            let $trigger = $('.feature-explanation-popup, .feature-explanation-popup *');
            if(!$trigger.is(event.target) && $trigger.has(event.target).length === 0){
                $('.feature-explanation-popup-main').not($(this).find('.feature-explanation-popup-main')).hide();
                $(this).parents('li').find('.feature-explanation-popup-main').show();
                $('body').addClass('feature-explanation-popup-visible');
            }
        });
        $(document).on('click', '.dotstore-upgrade-dashboard .popup-close-btn', function () {
            $(this).parents('.feature-explanation-popup-main').hide();
            $('body').removeClass('feature-explanation-popup-visible');
        });
        /** Upgrade Dashboard Script End */

        // Script for hide & show quick checkout btn row
        toggleQuickChkBtnLabelRow();
        $('input[name="wcdg_chk_details"], input[name="wcdg_chk_prod"]').change(function () {
            toggleQuickChkBtnLabelRow();
        });

        // Script for hide & show add to cart btn row
        hideShowFieldsOnToggle( 'input[name="wcdg_enable_cart_btn_label"]', '.wcdg_cart_btn_label_row' );
    });

    // Script for hide & show quick checkout btn row
    function toggleQuickChkBtnLabelRow() {
        let wcdg_chk_details = $('input[name="wcdg_chk_details"]').is(':checked');
        let wcdg_chk_prod = $('input[name="wcdg_chk_prod"]').is(':checked');
        if ( wcdg_chk_details || wcdg_chk_prod ) {
            $('.wcdg_chk_btn_label_row').show();
        } else {
            $('.wcdg_chk_btn_label_row').hide();
        }
    }

    // Hide & show fields/inner rows based on checkbox ON & OFF
    function hideShowFieldsOnToggle( checkbox, row ) {
        // Script for hide row on load
        let value = $(checkbox).is(':checked');
        if ( value ) {
            $(row).show();
        } else {
            $(row).hide();
        }

        // Script for hide row on change
        $(checkbox).change(function () {
            let checked = $(this).is(':checked');
            if ( checked ) {
                $(row).show();
            } else {
                $(row).hide();
            }
        });
    }

    // Set cookies
    function setCookie(name, value, minutes) {
        var expires = '';
        if (minutes) {
            var date = new Date();
            date.setTime(date.getTime() + (minutes * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + (value || '') + expires + '; path=/';
    }

    // Get cookies
    function getCookie(name) {
        let nameEQ = name + '=';
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }

    /** Script for Freemius upgrade popup */
    function upgradeToProFreemius( couponCode ) {
        let handler;
        handler = FS.Checkout.configure({
            plugin_id: '4703',
            plan_id: '7560',
            public_key:'pk_9edf804dccd14eabfd00ff503acaf',
            coupon: couponCode,
            show_reviews: true,
            show_refund_badge: true
        });
        handler.open({
            name: 'Digital Goods for WooCommerce Checkout',
            subtitle: 'You’re a step closer to our Pro features',
            licenses: jQuery('input[name="licence"]:checked').val(),
            purchaseCompleted: function( response ) {
                console.log (response);
            },
            success: function (response) {
                console.log (response);
            }
        });
    }
})(jQuery);