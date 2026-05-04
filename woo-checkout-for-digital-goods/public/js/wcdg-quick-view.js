(function ($) {
	'use strict';

	var $overlay = null;
	var $body = null;
	var lastTrigger = null;
	var addedToCartCloseTimer = null;

	function clearAddedToCartCloseTimer() {
		if (addedToCartCloseTimer) {
			clearTimeout(addedToCartCloseTimer);
			addedToCartCloseTimer = null;
		}
	}

	function hideCartNotice() {
		var $n = $('#wcdg-qv-notice');
		if ($n.length) {
			$n.attr('hidden', 'hidden').removeClass('wcdg-qv-notice--error').empty();
		}
	}

	function getOverlay() {
		$overlay = $('#wcdg-quick-view-overlay');
		$body = $('#wcdg-qv-body');
		return $overlay;
	}

	function trapFocus(e) {
		if (e.key !== 'Tab' || !$overlay || !$overlay.length) {
			return;
		}
		var $focusable = $overlay.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').filter(':visible');
		if ($focusable.length < 1) {
			return;
		}
		var first = $focusable[0];
		var last = $focusable[$focusable.length - 1];
		if (e.shiftKey && document.activeElement === first) {
			e.preventDefault();
			last.focus();
		} else if (!e.shiftKey && document.activeElement === last) {
			e.preventDefault();
			first.focus();
		}
	}

	function openModal() {
		var $el = getOverlay();
		if ($el.length) {
			$el.removeAttr('hidden').attr('aria-hidden', 'false');
			$('body').addClass('wcdg-qv-open');
			$(document).on('keydown.wcdgQv', function (e) {
				if (e.key === 'Escape') {
					closeModal();
				}
				trapFocus(e);
			});
			$el.find('.wcdg-qv-close').first().trigger('focus');
		}
	}

	function closeModal() {
		clearAddedToCartCloseTimer();
		hideCartNotice();
		removeStrayWcNoticesOutsideQuickView();
		var $el = getOverlay();
		if ($el.length) {
			$el.attr('hidden', 'hidden').attr('aria-hidden', 'true');
			$body.empty().addClass('wcdg-qv-loading');
			$('body').removeClass('wcdg-qv-open');
			$(document).off('keydown.wcdgQv');
			if (lastTrigger && lastTrigger.length) {
				lastTrigger.trigger('focus');
			}
		}
	}

	function getWcAjaxAddToCartUrl() {
		if (wcdgQuickView && wcdgQuickView.wcAjaxAddToCartUrl) {
			return wcdgQuickView.wcAjaxAddToCartUrl;
		}
		if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.wc_ajax_url) {
			return wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart');
		}
		return '';
	}

	function resolveProductIdForWcAjax($form) {
		var $hidden = $form.find('input[name="product_id"]').first();
		if ($hidden.length && $hidden.val()) {
			return $.trim($hidden.val());
		}
		var $ac = $form.find('input[name="add-to-cart"], button[name="add-to-cart"]').first();
		if ($ac.length && $ac.val()) {
			return $.trim($ac.val());
		}
		return '';
	}

	/**
	 * WooCommerce wc-ajax add_to_cart expects product_id to be the variation post ID when adding a variation.
	 * Variable forms POST parent id as product_id; replace with variation_id when a variation is selected.
	 *
	 * @return {string|null} Serialized POST data, null if variable product needs option selection first.
	 */
    function buildQuickViewAddToCartPayload($form) {
        var s = $form.serialize();
        var isVariationsForm = $form.hasClass('variations_form');
        var $vidInput = $form.find('input[name="variation_id"]').first();
        var vid = $vidInput.length ? parseInt($vidInput.val(), 10) : 0;
    
        if (isVariationsForm) {
            if (!vid || isNaN(vid) || vid <= 0) {
                return null;
            }
            s = s.replace(/(^|&)add-to-cart=\d+/, '');
            if (s.indexOf('product_id=') !== -1) {
                s = s.replace(/(^|&)product_id=\d+/, '$1product_id=' + vid);
            } else {
                s += (s.length ? '&' : '') + 'product_id=' + encodeURIComponent(String(vid));
            }
    
            return s;
        }
    
        // simple product fallback (keep as-is)
        if (s.indexOf('product_id=') === -1) {
            var pid = resolveProductIdForWcAjax($form);
            if (!pid) {
                return '';
            }
            s += (s.length ? '&' : '') + 'product_id=' + encodeURIComponent(pid);
        }
    
        return s;
    }
    // function buildQuickViewAddToCartPayload($form) {

    //     var s = $form.serialize();
    //     var isVariationsForm = $form.hasClass('variations_form');
    
    //     var $vidInput = $form.find('input[name="variation_id"]').first();
    //     var vid = $vidInput.length ? parseInt($vidInput.val(), 10) : 0;
    
    //     // 🔴 Stop if variation not selected
    //     if (isVariationsForm && (!vid || isNaN(vid) || vid <= 0)) {
    //         return null;
    //     }
    
    //     // ❌ Remove default Woo param
    //     s = s.replace(/(^|&)add-to-cart=\d+/, '');
    
    //     if (isVariationsForm) {
    
    //         // ✅ Get parent product ID (IMPORTANT)
    //         var parentId = resolveProductIdForWcAjax($form);
    
    //         if (!parentId) {
    //             return '';
    //         }
    
    //         // 🔥 FIX: product_id must be parent ID (NOT variation ID)
    //         if (s.indexOf('product_id=') !== -1) {
    //             s = s.replace(/(^|&)product_id=\d+/, '$1product_id=' + parentId);
    //         } else {
    //             s += '&product_id=' + encodeURIComponent(parentId);
    //         }
    
    //         // ✅ Ensure variation_id is present and correct
    //         if (s.indexOf('variation_id=') !== -1) {
    //             s = s.replace(/(^|&)variation_id=\d+/, '$1variation_id=' + vid);
    //         } else {
    //             s += '&variation_id=' + encodeURIComponent(vid);
    //         }
    
    //         return s;
    //     }
    
    //     // ✅ Simple product fallback
    //     if (s.indexOf('product_id=') === -1) {
    //         var pid = resolveProductIdForWcAjax($form);
    //         if (!pid) {
    //             return '';
    //         }
    //         s += '&product_id=' + encodeURIComponent(pid);
    //     }
    
    //     return s;
    // }
    

	/**
	 * Themes / WC may inject storefront notices on document.body when "added_to_cart" runs; remove those outside the quick view overlay.
	 */
	function removeStrayWcNoticesOutsideQuickView() {
		$('.woocommerce-notices-wrapper').each(function () {
			var $w = $(this);
			if (!$w.closest('#wcdg-quick-view-overlay').length) {
				$w.remove();
			}
		});
		$('.woocommerce-store-notice').each(function () {
			var $n = $(this);
			if (!$n.closest('#wcdg-quick-view-overlay').length) {
				$n.remove();
			}
		});
		// Some themes / handlers append a lone message to body after `added_to_cart`.
		$('body').children('.woocommerce-message, .woocommerce-error, .woocommerce-info').each(function () {
			var $m = $(this);
			if (!$m.closest('#wcdg-quick-view-overlay').length) {
				$m.remove();
			}
		});
	}

	function showCartNoticeSuccess(htmlMsg) {
		var $notice = $('#wcdg-qv-notice');
		if (!$notice.length) {
			return;
		}
		var msg = htmlMsg || (wcdgQuickView.i18n && wcdgQuickView.i18n.addedToCart ? wcdgQuickView.i18n.addedToCart : '');
		clearAddedToCartCloseTimer();
		$notice.removeClass('wcdg-qv-notice--error').removeAttr('hidden').html(
			'<div class="woocommerce-message" role="alert">' + $('<div/>').text(msg).html() + '</div>'
		);
		var delay =
			wcdgQuickView.addedToCartCloseMs && parseInt(wcdgQuickView.addedToCartCloseMs, 10) > 0
				? parseInt(wcdgQuickView.addedToCartCloseMs, 10)
				: 2000;
		addedToCartCloseTimer = setTimeout(function () {
			addedToCartCloseTimer = null;
			removeStrayWcNoticesOutsideQuickView();
		}, delay);
	}

	function showCartNoticeError(msg) {
		var $notice = $('#wcdg-qv-notice');
		if (!$notice.length) {
			return;
		}
		var text =
			msg ||
			(wcdgQuickView.i18n && wcdgQuickView.i18n.addToCartError
				? wcdgQuickView.i18n.addToCartError
				: wcdgQuickView.i18n && wcdgQuickView.i18n.error
					? wcdgQuickView.i18n.error
					: '');
		clearAddedToCartCloseTimer();
		$notice.addClass('wcdg-qv-notice--error').removeAttr('hidden').html(
			'<div class="woocommerce-error" role="alert">' + $('<div/>').text(text).html() + '</div>'
		);
	}

	function applyWcFragments(fragments) {
		if (!fragments || typeof fragments !== 'object') {
			return;
		}
		var useBlock = typeof $.fn.block === 'function' && typeof $.fn.unblock === 'function';
		if (useBlock) {
			$.each(fragments, function (key) {
				$(key).addClass('updating').fadeTo('400', '0.6').block({
					message: null,
					overlayCSS: { opacity: 0.6 },
				});
			});
		}
		$.each(fragments, function (key, value) {
			$(key).replaceWith(value);
			if (useBlock) {
				$(key).stop(true).css('opacity', '1').unblock();
			}
		});
		$(document.body).trigger('wc_fragments_loaded');
	}

	function ajaxAddToCartFromQuickView($form, $submitBtn) {
		var url = getWcAjaxAddToCartUrl();
		if (!url) {
			showCartNoticeError();
			return;
		}

		var data = buildQuickViewAddToCartPayload($form);
		if (data === null) {
			showCartNoticeError(
				wcdgQuickView.i18n && wcdgQuickView.i18n.selectVariation ? wcdgQuickView.i18n.selectVariation : ''
			);
			return;
		}
		if (data === '') {
			showCartNoticeError();
			return;
		}

		$submitBtn.addClass('loading').prop('disabled', true);
		$form.addClass('processing');

		$.ajax({
			type: 'POST',
			url: url,
			data: data + '&wcdg_quick_view=1',
			dataType: 'json',
		})
			.done(function (response) {
				$submitBtn.removeClass('loading').prop('disabled', false);
				$form.removeClass('processing');

				if (!response) {
					showCartNoticeError();
					return;
				}

				if (response.error && response.product_url) {
					showCartNoticeError(
						wcdgQuickView.i18n && wcdgQuickView.i18n.addToCartError
							? wcdgQuickView.i18n.addToCartError
							: ''
					);
					return;
				}

				// In the modal we never redirect to cart or product — same success UX for all WC settings.
				if (response.fragments) {
					applyWcFragments(response.fragments);
				}

				$(document.body).trigger('added_to_cart', [
					response.fragments || false,
					response.cart_hash || false,
					$submitBtn,
				]);

				// `added_to_cart` on body can make themes print WC notices on the shop page; strip those (not inside our overlay).
				removeStrayWcNoticesOutsideQuickView();
				setTimeout(removeStrayWcNoticesOutsideQuickView, 100);
				setTimeout(removeStrayWcNoticesOutsideQuickView, 400);

				var msg = wcdgQuickView.i18n && wcdgQuickView.i18n.addedToCart ? wcdgQuickView.i18n.addedToCart : '';
				showCartNoticeSuccess(msg);
			})
			.fail(function () {
				$submitBtn.removeClass('loading').prop('disabled', false);
				$form.removeClass('processing');
				showCartNoticeError();
			});
	}

	function initProductScripts() {
		if (typeof $.fn.wc_variation_form === 'function') {
			$body.find('.variations_form').each(function () {
				$(this).wc_variation_form();
			});
		}
		$(document.body).trigger('wcdg_quick_view_loaded', [$body]);
	}

	$(document).on('submit', '#wcdg-quick-view-overlay form.cart', function (e) {
		e.preventDefault();
		var $form = $(this);
		var $submit = $form.find('button[type="submit"].single_add_to_cart_button').first();
		if (!$submit.length) {
			$submit = $form.find('button[type="submit"]').first();
		}
		ajaxAddToCartFromQuickView($form, $submit);
	});

    $(document).on('input change', '#wcdg-quick-view-overlay input[name="quantity"]', function () {
        var qty = $(this).val() || 1;
    
        var $link = $('#wcdg-quick-view-overlay .wcdg-qv-quick-checkout a');
        var url = $link.attr('href');
    
        if (!url){ return; }
    
        // Check if quantity already exists
        if (url.indexOf('quantity=') !== -1) {
            // Replace existing quantity
            url = url.replace(/quantity=\d+/, 'quantity=' + qty);
        } else {
            // Add quantity for first time
            url += (url.indexOf('?') !== -1 ? '&' : '?') + 'quantity=' + qty;
        }
    
        $link.attr('href', url);
    });

	$(document).on('click', '.wcdg-quick-view-btn', function (e) {
		e.preventDefault();
		var $btn = $(this);
		lastTrigger = $btn;
		var pid = parseInt($btn.data('product_id'), 10);
		if (!pid || !wcdgQuickView) {
			return;
		}
		clearAddedToCartCloseTimer();
		hideCartNotice();
		removeStrayWcNoticesOutsideQuickView();
		var $el = getOverlay();
		if (!$el.length) {
			return;
		}
		$body.addClass('wcdg-qv-loading').html('<span class="wcdg-qv-spinner">' + (wcdgQuickView.i18n && wcdgQuickView.i18n.loading ? wcdgQuickView.i18n.loading : '…') + '</span>');
		openModal();

		$.post(
			wcdgQuickView.ajaxUrl,
			{
				action: 'wcdg_quick_view',
				nonce: wcdgQuickView.nonce,
				product_id: pid,
			}
		)
			.done(function (res) {
				if (res && res.success && res.data && res.data.html) {
					$body.removeClass('wcdg-qv-loading').html(res.data.html);
					var titleId = $body.find('[id^="wcdg-qv-title-"]').attr('id');
					if (titleId) {
						$el.find('.wcdg-qv-dialog').attr('aria-labelledby', titleId);
					}
					initProductScripts();
				} else {
					$body.removeClass('wcdg-qv-loading').html('<p class="wcdg-qv-error">' + (res && res.data && res.data.message ? res.data.message : '') + '</p>');
				}
			})
			.fail(function () {
				$body.removeClass('wcdg-qv-loading').html('<p class="wcdg-qv-error">' + (wcdgQuickView.i18n && wcdgQuickView.i18n.error ? wcdgQuickView.i18n.error : '') + '</p>');
			});
	});

	$(document).on('click', '.wcdg-qv-close, .wcdg-qv-backdrop', function (e) {
		e.preventDefault();
		closeModal();
	});

	$(document).on('click', '.wcdg-qv-dialog', function (e) {
		e.stopPropagation();
	});
})(jQuery);
