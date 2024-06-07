jQuery(document).ready(function ($) {
  $('.quick-view-button').on('click', function (e) {
      e.preventDefault();

      var productId = $(this).data('product-id');
      var ajaxUrl = fantasy_quick_view.ajax_url;

      $.ajax({
          url: ajaxUrl,
          type: 'POST',
          data: {
              action: 'quick_view_modal_content',
              product_id: productId
          },
          success: function (response) {
              if (response.success) {
                  $('#quick-view-modal .modal-content').html(response.data.html);
                  $('#quick-view-modal').show();
              } else {
                  console.error('Error loading quick view:', response.data.error);
                  alert('Error loading quick view: ' + response.data.error);
              }
          },
          error: function () {
              console.error('Error loading quick view');
              alert('Error loading quick view');
          }
      });
  });

  $('.modal-close').on('click', function () {
      $('#quick-view-modal').hide();
  });
});
