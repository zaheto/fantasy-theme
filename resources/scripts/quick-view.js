'use strict';

jQuery(document).ready(function($) {
  var ajaxInProgress = false; // Flag to prevent duplicate AJAX calls

  // Function to initialize WooCommerce variation form
  function initializeVariationForm() {
    $('.variations_form').each(function() {
      $(this).wc_variation_form();
      $(this).find('.variations select').change();
    });

    // Hide the quantity input and set it to 1 by default
    var quantityInput = $('.variations_form').find('input.qty');
    if (quantityInput.length > 0) {
      quantityInput.val(1).hide(); // Hide the quantity input
      console.log('Quantity input set to 1 and hidden.'); // Debugging
    }

    // Add listener for variation change
    $('form.variations_form').off('woocommerce_variation_select_change').on('woocommerce_variation_select_change', function() {
      var variation_id = $(this).find('input.variation_id').val();
      console.log('Variation ID changed to:', variation_id); // Debugging
      $('.single_add_to_cart_button').data('variation-id', variation_id);

      // Update button state
      updateAddToCartButtonState();
    });

    // Update variation ID on variation data fetch
    $('form.variations_form').off('found_variation').on('found_variation', function(event, variation) {
      $(this).find('input.variation_id').val(variation.variation_id);
      console.log('Variation ID found:', variation.variation_id); // Debugging

      // Update button state
      updateAddToCartButtonState();
    });

    // Check for initial variation selection
    $('form.variations_form').trigger('check_variations');
    var initialVariationId = $('form.variations_form').find('input.variation_id').val();
    console.log('Initial Variation ID:', initialVariationId); // Debugging

    // Initial button state update
    updateAddToCartButtonState();
  }

  // Function to update the "Add to Cart" button state
  function updateAddToCartButtonState() {
    var form = $('.variations_form');
    var variation_id = form.find('input.variation_id').val();
    var addToCartButton = form.find('.single_add_to_cart_button');

    if (variation_id && variation_id > 0) {
      addToCartButton.removeClass('disabled wc-variation-selection-needed').prop('disabled', false);
    } else {
      addToCartButton.addClass('disabled wc-variation-selection-needed').prop('disabled', true);
    }
  }

  // Open quick view modal
  $('.open-quick-view').on('click', function(e) {
    e.preventDefault();

    var productId = $(this).data('product-id');

    console.log('Opening quick view for product ID:', productId);

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
          $('#quick-view-modal').addClass('active');

          console.log('Quick view modal opened successfully.');

          // Ensure WooCommerce variation form is initialized
          initializeVariationForm();

          // Prevent default form submission and handle via AJAX
          $('.variations_form').off('submit').on('submit', function(e) {
            e.preventDefault();
            if (ajaxInProgress) {
              console.log('Duplicate submission prevented.'); // Debugging
              return; // Prevent duplicate submission
            }
            ajaxInProgress = true; // Set flag to indicate AJAX in progress

            var form = $(this);

            // Ensure the variation_id is updated
            var variation_id = form.find('input.variation_id').val();
            if (!variation_id) {
              alert('Please select a product option before adding to cart.');
              console.log('No variation selected. Form data:', form.serialize()); // Debugging
              ajaxInProgress = false; // Reset flag
              return;
            }

            // Set quantity to 1 explicitly
            form.find('input.qty').val(1);

            // Ensure WooCommerce validates the form
            form.trigger('check_variations');
            if (!form.find('input.variation_id').val()) {
              alert('Please select a valid product option.');
              console.log('Invalid variation. Form data:', form.serialize()); // Debugging
              ajaxInProgress = false; // Reset flag
              return;
            }

            // Clean the form data to avoid duplicate fields
            var formDataArray = form.serializeArray();
            var cleanFormData = {};
            formDataArray.forEach(function(item) {
              if (cleanFormData[item.name]) {
                if (!Array.isArray(cleanFormData[item.name])) {
                  cleanFormData[item.name] = [cleanFormData[item.name]];
                }
                cleanFormData[item.name].push(item.value);
              } else {
                cleanFormData[item.name] = item.value;
              }
            });

            // Ensure single quantity field
            cleanFormData['quantity'] = '1';

            var formData = $.param(cleanFormData);
            formData += '&add-to-cart=' + form.find('input[name="product_id"]').val();
            formData += '&action=woocommerce_ajax_add_to_cart';
            formData += '&nonce=' + quickViewAjax.nonce; // Add nonce to the form data

            console.log('Clean Form Data before AJAX:', formData); // Debugging

            $.ajax({
              url: quickViewAjax.ajaxurl,
              type: 'POST',
              data: formData,
              beforeSend: function() {
                console.log('AJAX request being sent with clean data:', formData);
                // Log to debug.log file
                $.ajax({
                  url: quickViewAjax.ajaxurl,
                  type: 'POST',
                  data: {
                    action: 'log_to_debug',
                    message: 'AJAX request being sent with clean data: ' + formData
                  }
                });
              },
              success: function(response) {
                console.log('AJAX Success:', response); // Debugging
                if (!response.error) {
                  // Update the cart fragments
                  $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);

                  // Close the modal
                  $('#quick-view-modal').removeClass('active');

                  // Open the cart drawer
                  $('body').addClass('drawer-open');

                  // Check the quantity in the mini cart
                  var miniCartQuantity = $('.fantasy-custom-quantity-mini-cart_input').val();
                  console.log('Mini Cart Quantity after adding:', miniCartQuantity); // Debugging

                  // Log to debug.log file
                  $.ajax({
                    url: quickViewAjax.ajaxurl,
                    type: 'POST',
                    data: {
                      action: 'log_to_debug',
                      message: 'Mini Cart Quantity after adding: ' + miniCartQuantity
                    }
                  });
                } else {
                  console.log('Error adding to cart:', response.error);
                  // Log to debug.log file
                  $.ajax({
                    url: quickViewAjax.ajaxurl,
                    type: 'POST',
                    data: {
                      action: 'log_to_debug',
                      message: 'Error adding to cart: ' + response.error
                    }
                  });
                }
                ajaxInProgress = false; // Reset flag after success
              },
              error: function(response) {
                console.log('AJAX Error:', response);
                // Log to debug.log file
                $.ajax({
                  url: quickViewAjax.ajaxurl,
                  type: 'POST',
                  data: {
                    action: 'log_to_debug',
                    message: 'AJAX Error: ' + JSON.stringify(response)
                  }
                });
                ajaxInProgress = false; // Reset flag after error
              }
            });
          });
        } else {
          console.log('Error:', response.data); // Debugging
          // Log to debug.log file
          $.ajax({
            url: quickViewAjax.ajaxurl,
            type: 'POST',
            data: {
              action: 'log_to_debug',
              message: 'Error: ' + response.data
            }
          });
        }
      },
      error: function(response) {
        console.log('AJAX Error:', response); // Debugging
        // Log to debug.log file
        $.ajax({
          url: quickViewAjax.ajaxurl,
          type: 'POST',
          data: {
            action: 'log_to_debug',
            message: 'AJAX Error: ' + JSON.stringify(response)
          }
        });
      }
    });
  });

  // Close quick view modal
  $(document).on('click', '.close-quick-view', function() {
    $('#quick-view-modal').removeClass('active');
  });

  // Close modal on overlay click
  $(document).on('click', '#quick-view-modal', function(e) {
    if (e.target.id === 'quick-view-modal') {
      $('#quick-view-modal').removeClass('active');
    }
  });

  // Debugging to ensure no duplicate listeners
  $('.variations_form').off('submit').on('submit', function(e) {
    console.log('Form submitted, AJAX in progress:', ajaxInProgress);
  });
});
