import domReady from '@roots/sage/client/dom-ready';

// if you're using a bundler, first import:
import Headroom from "headroom.js";
import initSliders from './sliders.js';


/**
 * Application entrypoint
 */
domReady(async () => {


  // grab an element
  var myElement = document.querySelector(".header");
  var options = {
    // vertical offset in px before element is first unpinned
    offset : 0,
    // or you can specify offset individually for up/down scroll
    offset: {
        up: 100,
        down: 250
    },
    // scroll tolerance in px before state changes
    tolerance : 0,
    // or you can specify tolerance individually for up/down scroll
    tolerance : {
        up : 5,
        down : 0
    },
  };
  // construct an instance of Headroom, passing the element
  var headroom = new Headroom(myElement, options);
  // initialise
  headroom.init();

  var canRunClickFunc = true;

  function makeTouchstartWithClick(event) {
    if (!canRunClickFunc) {
      return false;
    }
    setTimeout(function() {
      canRunClickFunc = true;
    }, 700);
    var elem = event.target;
    var elemp = elem.closest('.close-drawer');
    if (elem.classList.contains('close-drawer') || elemp) {
      document.querySelector('body').classList.remove('filter-open');
      document.querySelector('body').classList.remove('mobile-toggled');
      document.querySelector('body').classList.remove('drawer-open');
      return;
    }
    var elemp = elem.closest('.menu-toggle');
    if (elem.classList.contains('menu-toggle') || elemp) {
      event.stopPropagation();
      event.preventDefault();
      document.querySelector('body').classList.add('mobile-toggled');
      return;
    }
    if (elem.classList.contains('mobile-overlay')) {
      document.querySelector('body').classList.remove('filter-open');
      document.querySelector('body').classList.remove('mobile-toggled');
      return;
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('load', function(event) {
      var vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--vh', vh + 'px');
    });
    window.addEventListener('resize', function(event) {
      var vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--vh', vh + 'px');
    });

    document.addEventListener('click', function(event) {
      var is_inner = event.target.closest('.cart-popup');
      if (!event.target.classList.contains('cart-popup') && !is_inner) {
        document.querySelector('body').classList.remove('drawer-open');
        document.querySelector('body').classList.remove('disable-scroll');
      }
      var is_inner2 = event.target.closest('.cart-click');
      if (event.target.classList.contains('cart-click') || is_inner2) {
        var is_header = event.target.closest('.site-header-cart');
        if (is_header) {
          event.preventDefault();
          document.querySelector('body').classList.toggle('drawer-open');
          document.querySelector('body').classList.toggle('disable-scroll');
          document.getElementById('CartDrawer').focus();
        }
      }
      if (event.target.classList.contains('close-drawer')) {
        document.querySelector('body').classList.remove('drawer-open');
        document.querySelector('body').classList.remove('disable-scroll');
      }
      makeTouchstartWithClick(event);
    });

    // initSingleProductAjax();
  });

    // jQuery part for WooCommerce cart events
  jQuery(document).ready(function($) {
    $('body').on('added_to_cart', function(event, fragments, cart_hash) {
      if (!$('body').hasClass('elementor-editor-active')) {
        $('body').addClass('drawer-open');
        $('body').addClass('disable-scroll');
        $('#CartDrawer').focus();
      }
    });
  });

  // XMLHttpRequest interceptor for AJAX loading indicator
  var interceptor = (function(open) {
    XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
      this.addEventListener('readystatechange', function() {
        switch (this.readyState) {
          case 1:
            setTimeout(function() {
              document.querySelector('#ajax-loading').style.display = 'block';
            }, 200);
            break;
          case 4:
            setTimeout(function() {
              document.querySelector('#ajax-loading').style.display = 'none';
            }, 200);
            break;
        }
      }, false);
      if (async !== false) {
        async = true;
      }
      open.call(this, method, url, async, user, pass);
    };
  })(XMLHttpRequest.prototype.open);

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('#ajax-loading').style.display = 'none';
  });

  function cartDrawerTrapTabKey( event ) {
    var element = document.querySelector( 'body.drawer-open #CartDrawer' );
    if ( element ) {
      if ( event.key.toLowerCase() == 'escape' ) {
        document.querySelector( 'body' ).classList.remove( 'drawer-open' );
        return;
      } else if ( event.key.toLowerCase() == 'tab' ) {
        var inputs = ['a[href]','area[href]','input:not([disabled]):not([type="hidden"]):not([aria-hidden])','select:not([disabled]):not([aria-hidden])','textarea:not([disabled]):not([aria-hidden])','button:not([disabled]):not([aria-hidden])','iframe','object','embed','[contenteditable]','[tabindex]:not([tabindex^="-"])'];
        var nodes = element.querySelectorAll(inputs);
        var focusables = Array( ...nodes );
        if ( focusables.length === 0 ) {
          return;
        }
        focusables = focusables.filter( ( node ) => {
          return node.offsetParent !== null;
        } );
        if ( ! element.contains( document.activeElement ) ) {
          focusables[0].focus();
        } else {
          var focusedIndex = focusables.indexOf( document.activeElement );
          if ( event.shiftKey && focusedIndex === 0 ) {
            focusables[ focusables.length - 1 ].focus();
            event.preventDefault();
          }
          if ( ! event.shiftKey && focusables.length > 0 && focusedIndex === focusables.length - 1 ) {
            focusables[0].focus();
            event.preventDefault();
          }
        }
      }
    }
  }

  jQuery(function($) {

    // Select all links with hashes
    $('a[href*="#"]')
    // Remove links that don't actually link to anything
    .not('[href="#"]')
    .not('[href="#0"]')
    .click(function(event) {
        // On-page links
        if (
            location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
            &&
            location.hostname == this.hostname
        ) {
            // Figure out element to scroll to
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            // Does a scroll target exist?
            if (target.length) {
                // Only prevent default if animation is actually going to happen
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top
                }, 500, function() {
                    // Callback after animation
                    // Must change focus!
                    var $target = $(target);
                    $target.focus();
                    if ($target.is(":focus")) { // Checking if the target was focused
                        return false;
                    } else {
                        $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
                        $target.focus(); // Set focus again
                    };
                });
            }
        }
    });

    // Function to fade out WooCommerce messages
    function fadeOutWooCommerceMessages() {
      $('.woocommerce-message').each(function() {
        var $message = $(this);
        setTimeout(function() {
          $message.fadeOut('slow');
        }, 2000);
      });
    }

    // Observe changes to the body element and its descendants
    var observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        // Check if the mutation added nodes and if any of those nodes contain a WooCommerce message
        if (mutation.addedNodes.length > 0 && $(mutation.target).find('.woocommerce-message').length > 0) {
          fadeOutWooCommerceMessages();
        }
      });
    });

    // Start observing changes to the body element and its descendants
    observer.observe(document.body, { childList: true, subtree: true });

    setTimeout(function() {
      jQuery('.woocommerce-message').fadeOut('fast')
    }, 2000);

    $('.product-categories ul.children').each(function() {
        var parentLi = $(this).parent('li');
        parentLi.prepend('<span class="toggle-button"></span>');
    });

    $('.toggle-button').click(function() {
        $(this).toggleClass('open');
        $(this).siblings('ul.children').slideToggle();
    });

    // Open all parent categories of the current category
    $('.current-cat').parents('ul.children').each(function() {
        $(this).prev('.toggle-button').addClass('open');
        $(this).show();
    });

    // Open the first level of sub-categories of the current category
    $('.current-cat > .toggle-button').addClass('open');
    $('.current-cat > ul.children').show();

    $('#desktop-menu').on('click', function(e) {
      e.preventDefault()
      $('.main-nav').toggleClass('active')
    });

    if($(window).width() <= 1024) {
      $('ul.nav li').on("click", function () {
        $('ul.nav li').not($(this)).removeClass('active')
        $(this).toggleClass('active')
      })
    }

    $('.mobile-nav .dropdown-products').detach().insertAfter('.header .products a')

    $(document).on('click', '#close-cart-popup', function () {
      $('.overlay, .cart-popup').removeClass('active')
      $('.overlay, .mobile-nav').removeClass('active')
      $(document.body).removeClass('disable-scroll');
    });

    $('#search-menu').on('click', function(e) {
      e.preventDefault()
      $('.search-box').toggleClass('active')
    });

    $('.overlay').on('click', function() {
      $(document.body).removeClass('disable-scroll');
      $('#close-cart-popup').trigger('click')
      $('.cart-content .popup, .popup-video').removeClass('active')
      if($(window).width() <= 768) {
          $('.hamburger-button').removeClass('active')
          $('.main-nav').slideUp()
      }
    });

    $('#openTableSizeModal').on('click', function(e) {
      e.preventDefault()
      $('#tableSizeModal').addClass('open')
      $(document.body).addClass('disable-scroll');
    });

    $('#closeTableSizeModal').on('click', function(e) {
      e.preventDefault()
      $('#tableSizeModal').removeClass('open')
      $(document.body).removeClass('disable-scroll');
    });

    $('#mini-cart').on("click", function (e) {
      e.preventDefault()
      $('.overlay, .cart-popup').addClass('open')
      $(document.body).addClass('disable-scroll');
    });

    $('#mobile-menu').on("click", function (e) {
      e.preventDefault()
      $('.overlay, .mobile-nav').addClass('active')
      $(document.body).addClass('disable-scroll');
    });

    $('#toggleFilters').on('click', function(e) {
      e.preventDefault()
      $('#aside').toggleClass('open')
    });

    if ( $( 'body' ).first().hasClass( 'woocommerce-cart' ) ) {
      $('#mini-cart').on("click", function (e) {
        e.preventDefault()
          $('.overlay').addClass('hidden')
          $(document.body).addClass('auto');
      })
    }

    $('.cart-info').click(function (e) {
      e.preventDefault()
      $('.overlay, .cart-popup').addClass('active')
      $(document.body).addClass('disable-scroll');
    })

    $('#play-video').click(function(e) {
      e.preventDefault(); // Prevent default action of the link

      // Prepare the iframe using the videoSrc variable
      var iframe = $('<iframe/>', {
        'src': videoSrc,
        'width': '100%',
        'height': '500',
        'frameborder': '0',
        'allow': 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
        'allowfullscreen': true
      });

      // Append the iframe to the container
      $('#videoContainer').html(iframe);

      // Show the modal
      $('#videoModal').removeClass('hidden').addClass('flex');
    });

    // Close the modal
    $('.close-modal').click(function() {
      $('#videoModal').removeClass('flex').addClass('hidden');
      $('#videoContainer').html(''); // Remove the iframe
    });

    $('#quick-order').click(function(e) {
      e.preventDefault(); // Prevent default action of the link

      // Show the modal
      $('#quickModal').removeClass('hidden').addClass('block');
    });

    // Close the modal
    $('.close-modal').click(function() {
      $('#quickModal').removeClass('block').addClass('hidden');
    });

  });

  initSliders();



});

/**
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
if (import.meta.webpackHot) import.meta.webpackHot.accept(console.error);
