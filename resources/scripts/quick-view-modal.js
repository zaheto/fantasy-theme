jQuery(document).ready(function($) {
  $('body').on('click', '.quick-view-button', function(e) {
      e.preventDefault();
      var productId = $(this).data('product-id');
      console.log('Quick view button clicked. Product ID:', productId);

      $.ajax({
          url: fantasy_quick_view.ajax_url,
          type: 'POST',
          data: {
              action: 'fantasy_load_product_quick_view',
              product_id: productId
          },
          success: function(response) {
              console.log('AJAX response received:', response);
              if (response.success) {
                  $('#quick-view-content').html(response.data.html);
                  $('#quick-view-modal').removeClass('hidden');
                  console.log('Quick view modal content loaded.');
              } else {
                  alert(response.data.error);
                  console.error('Error loading quick view content:', response.data.error);
              }
          },
          error: function(xhr, status, error) {
              alert('Error loading quick view');
              console.error('AJAX request failed:', status, error);
          }
      });
  });

  // Close modal
  $('body').on('click', '.close-modal', function() {
      $('#quick-view-modal').addClass('hidden');
      console.log('Quick view modal closed.');
  });
});
