'use strict';

jQuery(document).ready(function($) {
  // Open quick view modal
  $('.open-quick-view').on('click', function(e) {
    e.preventDefault();

    var productId = $(this).data('product-id');

    $.ajax({
      url: quickViewAjax.ajaxurl,
      type: 'POST',
      data: {
        action: 'woosq_quickview',
        product_id: productId,
        nonce: quickViewAjax.nonce
      },
      success: function(response) {
        if (response.success) {
          $('#quick-view-product-details').html(response.data);
          $('#quick-view-modal').fadeIn();

          // Initialize WooCommerce variation form
          $('.variations_form').each(function() {
            $(this).wc_variation_form();
            $(this).find('.variations select').change();
          });

          // Add listener for variation change
          $('form.variations_form').on('woocommerce_variation_select_change', function() {
            var variation_id = $('input[name="variation_id"]').val();
            console.log('Variation ID changed to:', variation_id); // Debugging
            $('.single_add_to_cart_button').data('variation-id', variation_id);
          });

          // Prevent default form submission and handle via AJAX
          $('.variations_form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);

            var formData = form.serialize() + '&add-to-cart=' + form.find('input[name="product_id"]').val();
            formData += '&action=woocommerce_ajax_add_to_cart';
            formData += '&nonce=' + quickViewAjax.nonce; // Add nonce to the form data

            console.log('Form Data:', formData); // Debugging

            $.ajax({
              url: quickViewAjax.ajaxurl,
              type: 'POST',
              data: formData,
              success: function(response) {
                console.log('AJAX Success:', response); // Debugging
                if (!response.error) {
                  // Update the cart fragments
                  $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);

                  // Close the modal
                  $('#quick-view-modal').fadeOut();

                  // Open the cart drawer
                  $('body').addClass('drawer-open');
                } else {
                  console.log('Error adding to cart:', response.error);
                }
              },
              error: function(response) {
                console.log('AJAX Error:', response);
              }
            });
          });
        } else {
          console.log('Error:', response.data); // Debugging
        }
      },
      error: function(response) {
        console.log('AJAX Error:', response); // Debugging
      }
    });
  });

  // Close quick view modal
  $(document).on('click', '.close-quick-view', function() {
    $('#quick-view-modal').fadeOut();
  });

  // Close modal on overlay click
  $(document).on('click', '#quick-view-modal', function(e) {
    if (e.target.id === 'quick-view-modal') {
      $('#quick-view-modal').fadeOut();
    }
  });
});
