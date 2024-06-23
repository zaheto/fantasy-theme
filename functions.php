<?php

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/

if (! file_exists($composer = __DIR__.'/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'sage'));
}

require $composer;

/*
|--------------------------------------------------------------------------
| Register The Bootloader
|--------------------------------------------------------------------------
|
| The first thing we will do is schedule a new Acorn application container
| to boot when WordPress is finished loading the theme. The application
| serves as the "glue" for all the components of Laravel and is
| the IoC container for the system binding all of the various parts.
|
*/

if (! function_exists('\Roots\bootloader')) {
    wp_die(
        __('You need to install Acorn to use this theme.', 'sage'),
        '',
        [
            'link_url' => 'https://roots.io/acorn/docs/installation/',
            'link_text' => __('Acorn Docs: Installation', 'sage'),
        ]
    );
}

\Roots\bootloader()->boot();

/*
|--------------------------------------------------------------------------
| Register Sage Theme Files
|--------------------------------------------------------------------------
|
| Out of the box, Sage ships with categorically named theme files
| containing common functionality and setup to be bootstrapped with your
| theme. Simply add (or remove) files from the array below to change what
| is registered alongside Sage.
|
*/

collect(['setup', 'filters'])
->each(function ($file) {
    if (! locate_template($file = "app/{$file}.php", true, true)) {
        wp_die(
            /* translators: %s is replaced with the relative file path */
            sprintf(__('Error locating <code>%s</code> for inclusion.', 'sage'), $file)
        );
    }
});

/**
 * Disable the emoji's
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter out the tinymce emoji plugin.
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}


/**
 * Assign the fantasy version to a var
 */
$theme               = wp_get_theme( 'fantasy' );
$fantasy_version = $theme['Version'];
define( 'fantasy_VERSION', '1.0.0' );


// Additional Custom Field For Product Taxonomy
if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title' 	=> 'Page Custom Settings',
        'menu_title'	=> 'Theme settings',
        'menu_slug' 	=> 'theme-general-settings',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
    ));
}

function codeless_file_types_to_uploads($file_types){
    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg+xml';
    $file_types = array_merge($file_types, $new_filetypes );
    return $file_types;
}
add_filter('upload_mimes', 'codeless_file_types_to_uploads');

if (!function_exists('hs_sample_setup_header')) :
    function hs_sample_setup_header()
    {
        register_nav_menus(
            array(
                'main_menu' => esc_html__('Main navigation', 'sage'),            )
        );
    }
    add_action('after_setup_theme', 'hs_sample_setup_header');
endif;

add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);
function special_nav_class($classes, $item){
     if( in_array('current-menu-item', $classes) ){
             $classes[] = 'active';
     }
     return $classes;
}


/**
 * @snippet       WooCommerce User Registration Shortcode
 */

 add_shortcode( 'wc_reg_form_bbloomer', 'bbloomer_separate_registration_form' );

 function bbloomer_separate_registration_form() {
    if ( is_user_logged_in() ) return '<p>You are already registered</p>';
    ob_start();
    do_action( 'woocommerce_before_customer_login_form' );
    $html = wc_get_template_html( 'myaccount/form-login.php' );
    $dom = new DOMDocument();
    $dom->encoding = 'utf-8';
    $dom->loadHTML( utf8_decode( $html ) );
    $xpath = new DOMXPath( $dom );
    $form = $xpath->query( '//form[contains(@class,"register")]' );
    $form = $form->item( 0 );
    echo '<div class="woocommerce">';
    echo $dom->saveXML( $form );
    echo '</div>';
    wp_enqueue_script( 'wc-password-strength-meter' );
    return ob_get_clean();
 }

 /**
 * @snippet       WooCommerce User Login Shortcode
 */

add_shortcode( 'wc_login_form_bbloomer', 'bbloomer_separate_login_form' );

function bbloomer_separate_login_form() {
    if ( is_user_logged_in() ) return '<p>You are already logged in</p>';
    ob_start();
    do_action( 'woocommerce_before_customer_login_form' );
    echo '<div class="woocommerce">';
    echo woocommerce_login_form( array( 'redirect' => wc_get_page_permalink( 'myaccount' ) ) );
    echo '</div>';

   return ob_get_clean();
}

/**
 * @snippet Redirect Login/Registration to My Account
 */

 add_action( 'template_redirect', 'bbloomer_redirect_login_registration_if_logged_in' );

 function bbloomer_redirect_login_registration_if_logged_in() {
     if ( is_page() && is_user_logged_in() && ( has_shortcode( get_the_content(), 'wc_login_form_bbloomer' ) || has_shortcode( get_the_content(), 'wc_reg_form_bbloomer' ) ) ) {
         wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
         exit;
     }
 }


 /**
 * @snippet       Custom Redirect for Registrations @ WooCommerce My Account
 */

add_filter( 'woocommerce_registration_redirect', 'bbloomer_customer_register_redirect' );

function bbloomer_customer_register_redirect( $redirect_url ) {
   $redirect_url = '/shop';
   return $redirect_url;
}

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );


function by_wrap_title_open() {
	echo '<div id="buy-bottom" class="inside-product--top-wrap">';
}
add_action( 'woocommerce_before_single_product_summary', 'by_wrap_title_open', 1 );


function by_wrap_title_close() {
	echo '</div>';
}
add_action( 'woocommerce_after_single_product_summary', 'by_wrap_title_close', 10 );

/**
* WooCommerce Display Stock Availablity
*/

add_filter( 'woocommerce_get_availability', 'njengah_display_stock_availability', 1, 2);

function njengah_display_stock_availability( $availability, $_product ) {

   global $product;

   // Change In Stock Text
    if ( $_product->is_in_stock() ) {
        $availability['availability'] = __('In stock', 'fantasy');
    }

    // Change Out of Stock Text
    if ( ! $_product->is_in_stock() ) {
    	$availability['availability'] = __('Out of stock', 'fantasy');
    }

    return $availability;
}


//Product page breadcrumb
add_action( 'woocommerce_before_single_product', 'fantasy_breadcumb' );
function fantasy_breadcumb() {
   if ( function_exists('yoast_breadcrumb') ) {
     yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
   }
}



//Product loop open main container
add_action( 'woocommerce_before_shop_loop_item', 'fantasy_product_loop_main_container_open' );
function fantasy_product_loop_main_container_open() {
  echo '<div class="product-loop--container">';
}

//Product loop open div
add_action( 'woocommerce_shop_loop_item_title', 'fantasy_product_loop_open' );
function fantasy_product_loop_open() {
  echo '<div class="product-loop-title-container">';
}

//Product loop close div
add_action( 'woocommerce_after_shop_loop_item', 'fantasy_product_loop_close' );
function fantasy_product_loop_close() {
  echo '</div></div>';
}


//Product loop open div
add_action('woocommerce_before_add_to_cart_quantity', 'fantasy_product_button_open');
function fantasy_product_button_open() {
  echo '<section class="inside-product--buy-buttons">';
}

// Insert the wishlist button after the add to cart button but within the open/close div
add_action('woocommerce_after_add_to_cart_button', 'add_wishlist_button_inside_buy_buttons');
function add_wishlist_button_inside_buy_buttons() {
    global $product;
    if (function_exists('do_shortcode')) {
        $product_id = $product->get_id();
        echo do_shortcode('[woosw id="' . $product_id . '"]');
    }
}

//Product loop open div
add_action('woocommerce_after_add_to_cart_button', 'fantasy_product_button_close', 20);
function fantasy_product_button_close() {
  echo '</section>';
}


/**
 * @snippet       Hide ALL shipping rates in ALL zones when Free Shipping is available
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 6
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

 add_filter( 'woocommerce_package_rates', 'bbloomer_unset_shipping_when_free_is_available_all_zones', 9999, 2 );

 function bbloomer_unset_shipping_when_free_is_available_all_zones( $rates, $package ) {
    $all_free_rates = array();
    foreach ( $rates as $rate_id => $rate ) {
       if ( 'free_shipping' === $rate->method_id ) {
          $all_free_rates[ $rate_id ] = $rate;
          break;
       }
    }
    if ( empty( $all_free_rates )) {
       return $rates;
    } else {
       return $all_free_rates;
    }
 }

 add_filter( 'woocommerce_checkout_fields' , 'quadlayers_remove_checkout_fields' );
 function quadlayers_remove_checkout_fields( $fields ) {

    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['shipping']['shipping_state']);

    return $fields;

}

/**
 * @snippet       Removes shipping method labels @ WooCommerce Cart / Checkout
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 3.9
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

 add_filter( 'woocommerce_cart_shipping_method_full_label', 'bbloomer_remove_shipping_label', 9999, 2 );

 function bbloomer_remove_shipping_label( $label, $method ) {
     $new_label = preg_replace( '/^.+:/', '', $label );
     return $new_label;
 }

/**
 * @snippet       Product Images @ Woo Checkout
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 5
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

add_filter( 'woocommerce_cart_item_name', 'bbloomer_product_image_review_order_checkout', 9999, 3 );

function bbloomer_product_image_review_order_checkout( $name, $cart_item, $cart_item_key ) {
     if ( ! is_checkout() ) return $name;
     $product = $cart_item['data'];
     $thumbnail = $product->get_image( array( '50', '50' ), array( 'class' => 'alignleft' ) );
     return $thumbnail . $name;
}

// function uwc_new_address_one_placeholder( $fields ) {
//     $fields['address_1']['label'] = 'Адрес за доставка';
//     $fields['address_1']['placeholder'] = 'Моля, въведете желания адрес за доставка или офис на ЕКОНТ';

//     return $fields;
// }
// add_filter( 'woocommerce_default_address_fields', 'uwc_new_address_one_placeholder' );


/**
 * Remove password strength check.
 */
function iconic_remove_password_strength() {
    wp_dequeue_script( 'wc-password-strength-meter' );
}
add_action( 'wp_print_scripts', 'iconic_remove_password_strength', 10 );

// Show only lowest prices in WooCommerce variable products

add_filter( 'woocommerce_variable_sale_price_html', 'wpglorify_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wpglorify_variation_price_format', 10, 2 );

function wpglorify_variation_price_format( $price, $product ) {

// Main Price
$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
$price = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

// Sale Price
$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
sort( $prices );
$saleprice = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

if ( $price !== $saleprice ) {
$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
}
return $price;
}


add_action( 'woocommerce_before_shop_loop_item_title', 'action_template_loop_product_thumbnail', 9 );
function action_template_loop_product_thumbnail() {
    global $product;

    $file = get_field('archive_video', $product->get_id());

    if( isset($file['url']) && ! empty($file['url']) ) {
        remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

        echo '<video width="200" muted loop autoplay src="' . $file['url'] . '"></video>';
    }
}


function custom_table_size_modal() {

    // Get the table size link from the ACF custom field
    $table_size_link = get_field('table_size_link', 'options');

    if ($table_size_link) {
        // Output the link and modal HTML
        echo '<a href="#" id="openTableSizeModal" class="link table-size">Таблица с размери</a>';


        echo '<div id="tableSizeModal" class="modal">
                <div class="modal-wrap">
                    <div class="modal-content">
                        <div class="modal-head">
                            <h4>Таблица с размери</h4>
                            <button id="closeTableSizeModal" class="close-btn"> &times; </button>
                        </div>
                        <div class="modal-middle">
                            <img src="' . esc_url($table_size_link) . '"  />
                        </div>
                    </div>
                </div>
              </div>';
    }
}

add_action('woocommerce_product_meta_start', 'custom_table_size_modal', 31);


function custom_features_product() {
    $feature_bar = get_field('feature_bar', 'options');

    if ($feature_bar) {
        echo '<section class="feature-bar "><div class="container">';
        echo '<ul>';
        foreach ($feature_bar as $item) {
            echo '<li>';
            echo '<span><img src="' . esc_html($item['feature_icon']) . '" /></span>';
            echo '<div class="content"><h3>' . esc_html($item['feature_text']) . '</h3><p>' . esc_html($item['feature_description']) . '</p></div>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</div></section>';
    }

}

add_action('before_footer_content', 'custom_features_product', 31);


add_filter( 'woocommerce_single_product_carousel_options', 'cuswoo_update_woo_flexslider_options' );
/**
 * Filer WooCommerce Flexslider options - Add Navigation Arrows
 */
function cuswoo_update_woo_flexslider_options( $options ) {

    $options['directionNav'] = true;

    return $options;
}


//Add class to body when cart is empty
function rp_woo_empty_cart_classes( $classes ){
	global $woocommerce;
    if( is_cart() && WC()->cart->cart_contents_count == 0){
		$classes[] = 'woocommerce-cart-empty';
    }
    return $classes;
}
add_filter( 'body_class', 'rp_woo_empty_cart_classes' );


add_filter('woocommerce_is_purchasable', 'filter_is_purchasable_callback', 10, 2 );
add_filter('woocommerce_variation_is_purchasable', 'filter_is_purchasable_callback', 10, 2 );
function filter_is_purchasable_callback( $purchasable, $product ) {
    if ( $product->get_stock_status() === 'out_of_stock' ) {
        return false;
    }

    return $purchasable;
}



// Adjust price based on options
function adjust_price_based_on_options($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    foreach ($cart->get_cart() as $cart_item) {
        if (isset($cart_item['selected_options'])) {
            $price_adjustment = calculate_wpcpo_price_adjustment($cart_item['selected_options']);
            $cart_item['data']->set_price($cart_item['data']->get_price() + $price_adjustment);
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'adjust_price_based_on_options', 10, 1);



function calculate_wpcpo_price_adjustment($options) {
    $adjustment = 0;

    if (is_array($options) || is_object($options)) {
        foreach ($options as $option_name => $option_value) {
            // Placeholder function call, replace with actual function from the plugin
            $adjustment += WPCPO()->get_option_price($option_name, $option_value);
        }
    } else {
        error_log('Options are not an array or object: ' . print_r($options, true));
    }

    return $adjustment;
}

function filter_woocommerce_product_cross_sells_products_heading( $string ) {
    // New text
    $string = __( 'My new text', 'fantasy' );

    return $string;
}
add_filter( 'woocommerce_product_cross_sells_products_heading', 'filter_woocommerce_product_cross_sells_products_heading', 10, 1 );

function add_product_tags_to_body_class( $classes ) {
    if ( is_singular( 'product' ) ) {
        global $post;
        $product = wc_get_product( $post->ID );
        if ( $product ) {
            $tags = get_the_terms( $post->ID, 'product_tag' );
            if ( $tags && ! is_wp_error( $tags ) ) {
                // Loop through each tag and add its slug to the body class
                foreach ( $tags as $tag ) {
                    $classes[] = 'product-tag-' . sanitize_html_class( $tag->slug );
                }
            }
        }
    }
    return $classes;
}
add_filter( 'body_class', 'add_product_tags_to_body_class' );



// add_filter('wc_get_template_part', function($template, $slug, $name) {
//     $template_path = 'woocommerce/' . $slug . '-' . $name . '.blade.php';

//     if (file_exists(get_stylesheet_directory() . '/' . $template_path)) {
//         return get_stylesheet_directory() . '/' . $template_path;
//     }

//     return $template;
// }, 10, 3);


/////NEW CODE

if ( ! function_exists( 'fantasy_woo_cart_available' ) ) {
	/**
	 * Validates whether the Woo Cart instance is available in the request
	 *
	 * @since 2.6.0
	 * @return bool
	 */
	function fantasy_woo_cart_available() {
		$woo = WC();
		return $woo instanceof \WooCommerce && $woo->cart instanceof \WC_Cart;
	}
}

if ( ! function_exists( 'fantasy_is_woocommerce_activated' ) ) {
	/**
	 * Query WooCommerce activation
	 */
	function fantasy_is_woocommerce_activated() {
		$activated = class_exists( 'WooCommerce' ) ? true : false;
		if (!$activated) {
			error_log('WooCommerce is not activated');
		}
		return $activated;
	}
}

/**
 * Enqueue quantity.js script only on single product and cart pages.
 */
function fantasy_enqueue_quantity_script() {
    if ( is_product() || is_cart() ) {
        wp_enqueue_script( 'fantasy-quantity', get_template_directory_uri() . '/resources/scripts/quantity.js', array(), '1.1.4', true );
    }
}
add_action( 'wp_enqueue_scripts', 'fantasy_enqueue_quantity_script' );

/**
* Checks if ACF is active.
*
* @return boolean
*/
if ( ! function_exists( 'fantasy_is_acf_activated' ) ) {
	/**
	 * Query ACF activation.
	 */
	function fantasy_is_acf_activated() {
		return class_exists( 'acf' ) ? true : false;
	}
}


if ( ! function_exists( 'fantasy_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments
	 * Ensure cart contents update when products are added to the cart via AJAX
	 *
	 * @param  array $fragments Fragments to refresh via AJAX.
	 * @return array            Fragments to refresh via AJAX
	 */
	function fantasy_cart_link_fragment( $fragments ) {
		global $woocommerce;

		ob_start();
		fantasy_cart_link();
		$fragments['div.cart-click'] = ob_get_clean();

		return $fragments;
	}
}

if ( ! function_exists( 'fantasy_cart_link' ) ) {
	/**
	 * Cart Link
	 * Displayed a link to the cart including the number of items present and the cart total
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function fantasy_cart_link() {


		if ( ! fantasy_woo_cart_available() ) {
			error_log('WooCommerce cart is not available');
			return;
		}

        $cart_subtotal = WC()->cart->get_cart_subtotal();
		$cart_contents_count = WC()->cart->get_cart_contents_count();
		// error_log('Cart Subtotal: ' . $cart_subtotal);
		// error_log('Cart Contents Count: ' . $cart_contents_count);

		?>

        <div class="cart-click">
            <a class="cart-contents" href="#" title="<?php esc_attr_e( 'View your shopping cart', 'fantasy' ); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.5 14.25C8.5 16.17 10.08 17.75 12 17.75C13.92 17.75 15.5 16.17 15.5 14.25" stroke="#000000" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.81 2L5.19 5.63" stroke="#000000" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15.19 2L18.81 5.63" stroke="#000000" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 7.84998C2 5.99998 2.99 5.84998 4.22 5.84998H19.78C21.01 5.84998 22 5.99998 22 7.84998C22 9.99998 21.01 9.84998 19.78 9.84998H4.22C2.99 9.84998 2 9.99998 2 7.84998Z" stroke="#000000" stroke-width="1.5"/>
                    <path d="M3.5 10L4.91 18.64C5.23 20.58 6 22 8.86 22H14.89C18 22 18.46 20.64 18.82 18.76L20.5 10" stroke="#000000" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <!-- <span class="amount"> -->
                    <?php //echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?>
                <!-- </span> -->
                <span class="count"><?php echo wp_kses_post( /* translators: cart count */ sprintf( _n( '%d', '%d', WC()->cart->get_cart_contents_count(), 'fantasy' ), WC()->cart->get_cart_contents_count() ) ); ?></span>
            </a>
        </div>
		<?php
	}
}

if ( ! function_exists( 'fantasy_header_cart' ) ) {
	/**
	 * Display Header Cart
	 *
	 * @since  1.0.0
	 * @uses  fantasy_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function fantasy_header_cart() {
			?>
		<ul class="site-header-cart menu">
			<li><?php fantasy_cart_link(); ?></li>
		</ul>
			<?php
	}
}

// Hook the header cart function to an action hook
add_action( 'fantasy_minicart_header', 'fantasy_header_cart' );

function fantasy_add_header_class_to_body($classes) {
    // Retrieve the layout design group
    $layout_design = get_field('layout_design', 'option');

    // Access the header design within the group
    $header_design = $layout_design['choose_header_design'] ?? 'Header-1';
    $header_class = 'header-design-1';

    if ($header_design == 'Header-2') {
        $header_class = 'header-design-2';
    } elseif ($header_design == 'Header-3') {
        $header_class = 'header-design-3';
    }

    $classes[] = $header_class;
    return $classes;
}
add_filter('body_class', 'fantasy_add_header_class_to_body');


if ( ! function_exists( 'fantasy_header_cart_drawer' ) ) {
	/**
	 * Display Header Cart Drawer
	 *
	 * @since  1.0.0
	 * @uses  fantasy_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function fantasy_header_cart_drawer() {

        ?>


		<div tabindex="-1" id="CartDrawer" class="cart-popup" role="dialog" aria-label="Cart drawer">

			<div id="ajax-loading">
				<div class="fantasy-loader">
					<div class="spinner">
					<div class="bounce1"></div>
					<div class="bounce2"></div>
					<div class="bounce3"></div>
					</div>
				</div>
			</div>

            <?php do_action( 'fantasy_before_cart_popup' ); ?>
			<div class="cart-heading"><?php echo __('Your cart', 'fantasy'); ?></div>
                <button type="button" aria-label="Close drawer" class="close-drawer w-[28px] h-[28px]">
                    <span aria-hidden="true" class="w-[28px] h-[28px]"><svg class="w-[28px] h-[28px]" width="24" height="24" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 19L19 7" stroke="#292D32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 19L7 7" stroke="#292D32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                </button>

			<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>

		</div>

        <?php
    }
}

// Function to fetch the ACF settings for the mini cart.
function get_mini_cart_settings() {
    return get_field('mini_cart_settings', 'option');
}

/**
 * Output the announcement bar at the end of the mini cart.
 */
function fantasy_add_announce_bar_to_mini_cart() {
    // Check if the cart is not empty
    if (!WC()->cart->is_empty() && get_field('add_announce_to_mini_cart', 'option')) {
        if (get_field('announce_bar_header', 'option')) : ?>
            <section class="announce-bar">
                <ul>
                    <?php foreach (get_field('announce_bar_header', 'option') as $item) : ?>
                        <li><?php echo $item['annonce_text']; ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif;
    }
}

// Hook into the mini cart to add the announcement bar at the end.
add_action('woocommerce_before_mini_cart', 'fantasy_add_announce_bar_to_mini_cart');



/**
 * Display cross-sell products in the mini cart.
 */
function fantasy_minicart_cross_sells() {
    $cross_sells = WC()->cart->get_cross_sells();
    if (empty($cross_sells)) {
        return;
    }

    $args = array(
        'posts_per_page' => apply_filters('woocommerce_cross_sells_total', 4),
        'orderby'        => 'rand',
        'post_type'      => 'product',
        'post__in'       => $cross_sells,
    );

    $cross_sells_query = new WP_Query($args);
    if (!$cross_sells_query->have_posts()) {
        return;
    }

    echo '<div class="cross-sells">';
    echo '<h2>' . __('You may be interested in', 'fantasy') . '</h2>';
    echo '<ul class="products">';

    while ($cross_sells_query->have_posts()) {
        $cross_sells_query->the_post();
        wc_get_template_part('content', 'product');
    }

    echo '</ul>';
    echo '</div>';

    wp_reset_postdata();
}

// Hook cross-sell display into the mini cart.
add_action('woocommerce_mini_cart_contents', 'fantasy_minicart_cross_sells', 20);

/**
 * Remove view cart button from mini cart.
 */
// function fantasy_remove_view_cart_minicart() {

//         remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );

// }
// add_action( 'woocommerce_widget_shopping_cart_buttons', 'fantasy_remove_view_cart_minicart', 1 );

if ( class_exists( 'WooCommerce' ) ) {
	/**
	 * Adds a body class to just the Shop landing page.
	 */
	function fantasy_shop_body_class( $classes ) {
		if ( is_shop() ) {
			$classes[] = 'shop';
		}
		return $classes;
	}

	add_filter( 'body_class', 'fantasy_shop_body_class' );
}



/**
 * Ajax get variable product sale label prices.
 */
function fantasy_get_sale_prices() {
	$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : 0;
	$ajax       = array();
	$percents   = array();
	if ( $product_id ) {
		$_product = wc_get_product( $product_id );
		if ( $_product && $_product->is_type( 'variable' ) ) {
			$prices = $_product->get_variation_prices();
			if ( count( $prices ) ) {
				foreach ( $prices['price'] as $variation_id => $price ) {
					$sale_price    = $prices['sale_price'][ $variation_id ];
					$regular_price = $prices['regular_price'][ $variation_id ];
					if ( $regular_price !== $price ) {
						$percentage = round( 100 - ( $sale_price / $regular_price * 100 ) );
						if ( $percentage ) {
							$percents[ $variation_id ] = '-' . $percentage . '%';
						}
					}
				}
			}
		}
	}
	$ajax['percents'] = $percents;

	wp_send_json( $ajax );
}
add_action( 'wp_ajax_fantasy_get_sale_prices', 'fantasy_get_sale_prices' );
add_action( 'wp_ajax_nopriv_fantasy_get_sale_prices', 'fantasy_get_sale_prices' );

/**
 * Get variable product sale label prices script.
 */
function fantasy_get_sale_prices_script(){
	global $product;
	if ( ! is_product() ) {
		return;
	}
	if ( ! $product ) {
		return;
	}
	if ( ! $product->is_type( 'variable' ) ) {
		return;
	}
	if ( ! $product->is_on_sale() ) {
		return;
	}


		return;

	?>
<script type="text/javascript">
var fantasy_sales = null;
jQuery( document ).ready( function( $ ) {
	var fantasy_sale_lbl = $( '.summary .sale-item.product-label' );
	fantasy_sale_lbl.css( 'visibility', 'hidden' );
	$.ajax( {
		type: 'POST',
		url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
		data: { 'action': 'fantasy_get_sale_prices', 'product_id': <?php echo esc_attr( $product->get_id() ); ?> },
		success: function( json ) {
			fantasy_sales = json.percents;
			fantasy_update_variable_sale_badge();
		}
	} );
	$( '.summary input.variation_id' ).change( function() {
		fantasy_update_variable_sale_badge();
	} );
	function fantasy_update_variable_sale_badge() {
		var variation_id = $( '.summary input.variation_id' ).val();
		if ( '' != variation_id && fantasy_sales && fantasy_sales.hasOwnProperty( variation_id ) ) {
			fantasy_sale_lbl.html( fantasy_sales[variation_id] ).css( 'visibility', 'visible' );
		} else {
			fantasy_sale_lbl.css( 'visibility', 'hidden' );
		}
	}
} );
</script>
	<?php
}
add_action( 'wp_footer', 'fantasy_get_sale_prices_script', 999 );

/**
 * Single Product - exclude from Jetpack's Lazy Load.
 */
function is_lazyload_activated() {
	$condition = is_product();
	if ( $condition ) {
		return false;
	} return true;
}

add_filter( 'lazyload_is_enabled', 'is_lazyload_activated', 10, 3 );


/**
 * Show cart widget on all pages.
 */
add_filter( 'woocommerce_widget_cart_is_hidden', 'fantasy_always_show_cart', 40, 0 );

/**
 * Function to always show cart.
 */
function fantasy_always_show_cart() {
	return false;
}

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
//add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 7 );

/**
 * Single Product Page - Add a section wrapper start.
 */
add_action( 'woocommerce_before_single_product_summary', 'fantasy_product_content_wrapper_start', 5 );
function fantasy_product_content_wrapper_start() {
	echo '<div class="product-details-wrapper">';
}

/**
 * Single Product Page - Add a section wrapper end.
 */
add_action( 'woocommerce_single_product_summary', 'fantasy_product_content_wrapper_end', 60 );
function fantasy_product_content_wrapper_end() {
	echo '</div><!--/product-details-wrapper-end-->';
}

add_action( 'woocommerce_after_single_product_summary', 'fantasy_related_content_wrapper_start', 10 );
add_action( 'woocommerce_after_single_product_summary', 'fantasy_related_content_wrapper_end', 60 );

/**
 * Single Product Page - Related products section wrapper start.
 */
function fantasy_related_content_wrapper_start() {
	echo '<section class="related-wrapper">';
}


/**
 * Single Product Page - Related products section wrapper end.
 */
function fantasy_related_content_wrapper_end() {
	echo '</section>';
}


if ( ! function_exists( 'fantasy_pdp_ajax_atc' ) ) {
	/**
	 * PDP/Single product ajax add to cart.
	 */
	function fantasy_pdp_ajax_atc() {
		$sku = '';
		if ( isset( $_POST['variation_id'] ) ) {
			$sku = $_POST['variation_id'];
		}
		$product_id = $_POST['add-to-cart'];
		if ( empty( $sku ) ) {
			$sku = $product_id;
		}

		ob_start();
		wc_print_notices();
		$notices = ob_get_clean();
		ob_start();
		woocommerce_mini_cart();
		$fantasy_mini_cart = ob_get_clean();
		$fantasy_atc_data  = array(
			'notices'   => $notices,
			'fragments' => apply_filters(
				'woocommerce_add_to_cart_fragments',
				array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $fantasy_mini_cart . '</div>',
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() ),
		);
		// if GA Pro is installed, send an atc event.
		//if ( class_exists( 'WC_Google_Analytics_Pro' ) ) {
		//	wc_google_analytics_pro()->get_integration()->ajax_added_to_cart( $sku );
		//}
		do_action( 'woocommerce_ajax_added_to_cart', $sku );

		wp_send_json( $fantasy_atc_data );
		die();
	}
}

add_action( 'wc_ajax_fantasy_pdp_ajax_atc', 'fantasy_pdp_ajax_atc' );
add_action( 'wc_ajax_nopriv_fantasy_pdp_ajax_atc', 'fantasy_pdp_ajax_atc' );


if ( ! function_exists( 'fantasy_pdp_ajax_atc_enqueue' ) ) {

    /**
     * Enqueue assets for PDP/Single product ajax add to cart.
     */
    function fantasy_pdp_ajax_atc_enqueue() {
        if ( is_product() ) {

            wp_enqueue_script( 'fantasy-ajax-script', get_template_directory_uri() . '/resources/scripts/single-product-ajax.js', array( 'jquery' ), '1.0.0', true );
            wp_localize_script(
                'fantasy-ajax-script',
                'fantasy_ajax_obj',
                array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'nonce'   => wp_create_nonce( 'ajax-nonce' ),
                )
            );
        }
    }
}

// Hook the function to enqueue scripts
add_action( 'wp_enqueue_scripts', 'fantasy_pdp_ajax_atc_enqueue' );




/**
 * Custom markup around cart field.
 */
// function fantasy_cart_custom_field() {

// 	if ( is_active_sidebar( 'cart-field' ) ) :
// 		echo '<div class="cart-custom-field">';
// 		echo 'TEST TEST TEST CART TEXT';
// 		echo '</div>';
// 	endif;

// }



/**
 *  Quantity selectors for fantasy mini cart
 *
 * @package fantasy
 *
 * Description: Adds quantity buttons for the fantasy mini cart
 * Version: 1.0
 */


/**
 * Add minicart quantity fields
 *
 * @param  string $html          cart html.
 * @param  string $cart_item     cart item.
 * @param  string $cart_item_key cart item key.
 */
function add_minicart_quantity_fields( $html, $cart_item, $cart_item_key ) {

    $product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $cart_item['data'] ), $cart_item, $cart_item_key );
    $_product      = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    $max_qty       = $_product->get_max_purchase_quantity();

    $out = '<div class="fantasy-custom-quantity-mini-cart_container">
                <div class="fantasy-custom-quantity-mini-cart">
                <span tabindex="0" role="button" aria-label="Reduce quantity" class="fantasy-custom-quantity-mini-cart_button quantity-down">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                    </svg>
                </span>
                <input aria-label="' . esc_attr( __( 'Quantity input', 'fantasy' ) ) . '" class="fantasy-custom-quantity-mini-cart_input" data-cart_item_key="' . $cart_item_key . '" type="number" min="1" ' . ( -1 !== $max_qty ? 'max="' . $max_qty . '"' : '' ) . ' step="1" value="' . $cart_item['quantity'] . '">
                <span tabindex="0" role="button" aria-label="Increase quantity" class="fantasy-custom-quantity-mini-cart_button quantity-up">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </span>
            </div></div>';

    return sprintf(
        '%2$s %1$s',
        $out,
        $product_price
    );

}


add_filter( 'woocommerce_widget_cart_item_quantity', 'add_minicart_quantity_fields', 10, 3 );


if ( ! function_exists( 'minicart_fantasy_update_mini_cart' ) ) {




    /**
     * Minicart fantasy update mini cart.
     */
    function minicart_fantasy_update_mini_cart() {

        $data = $_POST['data']; // phpcs:ignore
        if ( ! WC()->cart->is_empty() ) {
            foreach ( $data as $item_key => $item_qty ) {
                $_cart_item = WC()->cart->get_cart_item( $item_key );
                if ( ! empty( $_cart_item ) ) {
                    $_product = apply_filters( 'woocommerce_cart_item_product', $_cart_item['data'], $_cart_item, $item_key );
                    $max_qty  = $_product->get_max_purchase_quantity();
                    if ( -1 !== $max_qty && $item_qty > $max_qty ) {
                        $item_qty = $max_qty;
                    }
                    if ( $item_qty > 0 ) {
                        WC()->cart->set_quantity( $item_key, $item_qty, true );
                    }
                }
            }
        }
        wp_send_json_success();
    }

}

add_action( 'wp_ajax_cg_fantasy_update_mini_cart', 'minicart_fantasy_update_mini_cart' );
add_action( 'wp_ajax_nopriv_cg_fantasy_update_mini_cart', 'minicart_fantasy_update_mini_cart' );


if ( ! function_exists( 'minicart_fantasy_get_styles' ) ) {
/**
 * Enqueue scripts
 */
function minicart_fantasy_get_scripts() {
    wp_enqueue_script( 'custom-fantasy-quantity-js', get_theme_file_uri( '/resources/scripts/minicart-quantity.js' ), array( 'jquery' ), time(), true );
}
}
add_action( 'wp_enqueue_scripts', 'minicart_fantasy_get_scripts', 30 );


/**
* Option to automatically update the cart page quantity without clicking "Update".
*
* @since 2.6.6
*/
add_action( 'wp_footer', 'fantasy_cart_ajax_update_quantity' );

function fantasy_cart_ajax_update_quantity() {

    if ( is_cart() || ( is_cart() && is_checkout() ) ) {
        wc_enqueue_js('
            var timeout;
            jQuery("div.woocommerce").on("change keyup mouseup", "input.qty, select.qty", function(){
                if (timeout != undefined) clearTimeout(timeout);
                if (jQuery(this).val() == "") return;
                timeout = setTimeout(function() {
                    jQuery("[name=\"update_cart\"]").trigger("click");
                }, );
            });

        ');
    }
}

add_filter( 'body_class', 'fantasy_cart_ajax_update_quantity_class');
function fantasy_cart_ajax_update_quantity_class( $classes ) {

    if ( is_cart() || ( is_cart() && is_checkout() ) ) {
            $classes[] = 'fantasy-ajax-cart';
    }

return $classes;
}




/**
 * Add free shipping notification to mini cart.
 */
function custom_fsn_add_mini_cart() {
// Fetch the ACF settings for mini cart
$mini_cart_settings = get_mini_cart_settings();

if (!WC()->cart->is_empty()) {
    // Check if the free shipping notification should be shown
    $show_free_shipping_notification = isset($mini_cart_settings['onoff_free_shipping_notification_-_mini_cart']) ? $mini_cart_settings['onoff_free_shipping_notification_-_mini_cart'] : false;

    if ($show_free_shipping_notification) {
        custom_free_shipping_notification('mini-cart');
    }
} else {
    fantasy_empty_mini_cart($mini_cart_settings);
}
}
add_action('woocommerce_before_mini_cart', 'custom_fsn_add_mini_cart', 20);

if ( ! function_exists( 'fantasy_upsell_display' ) ) {
/**
 * Upsells
 * Replace the default upsell function with our own which displays the correct number product columns
 *
 * @since   1.0.0
 * @return  void
 * @uses    woocommerce_upsell_display()
 */
function fantasy_upsell_display() {
    $columns = apply_filters( 'fantasy_upsells_columns', 4 );
    woocommerce_upsell_display( -1, $columns );
}
}

/**
 * Free shipping notification.
 *
 * @param string $type Type of notification.
 */
function custom_free_shipping_notification($type) {
    if (WC()->cart->is_empty()) {
        return;
    }

    $packages = WC()->cart->get_shipping_packages();
    $package = reset($packages);

    $min_amount = 0;
    $progressPercentage = 0;
    $free_shipping_available = false;

    if ($package) {
        $zone = wc_get_shipping_zone($package);
        if ($zone) {
            $shippingMethods = $zone->get_shipping_methods(true);
            $shippingCartTotal = floatval(WC()->cart->shipping_total ?? 0);
            $cartTotal = floatval(WC()->cart->total ?? 0);

            foreach ($shippingMethods as $method) {
                if ('free_shipping' === $method->id) {
                    $min_amount = floatval($method->get_option('min_amount') ?? 0);
                    $awayFromFreeDelivery = $min_amount - ($cartTotal - $shippingCartTotal);
                    $free_shipping_available = true;
                    break;
                }
            }
        }
    }

    if ($min_amount > 0) {
        $progressPercentage = ($cartTotal / $min_amount) * 100;
        if ($progressPercentage > 100) {
            $progressPercentage = 100;
        }
    } else {
        $awayFromFreeDelivery = 0;
    }

    if ($free_shipping_available) {
        ?>
        <div class="free-delivery-bar--cart">
            <?php if ($cartTotal <= $min_amount) : ?>
                <div class="bar-body">
                    <span style="width: <?php echo esc_attr($progressPercentage); ?>%;"></span>
                </div>
                <p>👋 <?php esc_html_e('You are ', 'fantasy'); ?> <span data-min="<?php echo esc_attr($min_amount); ?>" data-total="<?php echo esc_attr($cartTotal); ?>" data-shipping="<?php echo esc_attr($shippingCartTotal); ?>"><?php echo wc_price($awayFromFreeDelivery); ?></span> <?php esc_html_e(' away from free delivery', 'fantasy'); ?></p>
            <?php else : ?>
                <div class="bar-body">
                    <span style="width: 100%;"></span>
                </div>
                <p class="unlocked flex gap-2 items-center"><span class="icon icon-tick-circle"></span> <?php esc_html_e('Congrats! You\'ve reached free shipping.', 'fantasy'); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}


/**
 * Display custom content when mini cart is empty.
 */
function fantasy_empty_mini_cart($mini_cart_settings) {
if (WC()->cart->is_empty()) {
    echo '<div class="fantasy-empty-mini-cart">';
    echo '<svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M13.39 17.36L10.64 14.61" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> <path d="M13.36 14.64L10.61 17.39" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8.81 2L5.19 5.63" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> <path d="M15.19 2L18.81 5.63" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/> <path d="M2 7.84998C2 5.99998 2.99 5.84998 4.22 5.84998H19.78C21.01 5.84998 22 5.99998 22 7.84998C22 9.99998 21.01 9.84998 19.78 9.84998H4.22C2.99 9.84998 2 9.99998 2 7.84998Z" stroke="#292D32" stroke-width="1.5"/> <path d="M3.5 10L4.91 18.64C5.23 20.58 6 22 8.86 22H14.89C18 22 18.46 20.64 18.82 18.76L20.5 10" stroke="#292D32" stroke-width="1.5" stroke-linecap="round"/> </svg>';
    echo '<h4>'. __('Your bag is empty', 'fantasy') .'</h4>';

    // Display the selected categories
    if (!empty($mini_cart_settings['choose_emtpy_cart_category'])) {
        echo '<ul class="empty-cart-categories">';
        foreach ($mini_cart_settings['choose_emtpy_cart_category'] as $category_id) {
            $category = get_term($category_id, 'product_cat');
            if ($category) {
                echo '<li><a class="btn btn-main btn-block whitespace-nowrap" href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a></li>';
            }
        }
        echo '</ul>';
    }

    // Display the custom button
    if (!empty($mini_cart_settings['add_custom_button'])) {
        echo '<a href="' . esc_url($mini_cart_settings['add_custom_button']['url']) . '" class="custom-button btn btn-main-o btn-block whitespace-nowrap">' . esc_html($mini_cart_settings['add_custom_button']['title']) . '</a>';
    }

    echo '</div>';
}
}

/**
 * JavaScript to dynamically update the free shipping notification bar.
 */
function custom_fsn_enqueue_scripts() {
if (is_cart()) {
    wp_enqueue_script('custom-fsn-scripts', get_template_directory_uri() . '/resources/scripts/free-shipping.js', array('jquery'), null, true);
}
}
add_action('wp_enqueue_scripts', 'custom_fsn_enqueue_scripts');


/**
 * Remove default WooCommerce product link open
 *
 * @see get_the_permalink()
 */
function fantasy_remove_woocommerce_template_loop_product_link_open() {
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
}
add_action( 'wp_head', 'fantasy_remove_woocommerce_template_loop_product_link_open' );


/**
 * Remove default WooCommerce product link close
 *
 * @see get_the_permalink()
 */
function fantasy_remove_woocommerce_template_loop_product_link_close() {
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
}
add_action( 'wp_head', 'fantasy_remove_woocommerce_template_loop_product_link_close' );


/**
 * Open link before the product thumbnail image
 *
 * @see get_the_permalink()
 */
function fantasy_template_loop_image_link_open() {
echo '<a href="' . get_the_permalink() . '" title="' . get_the_title() . '" class="group woocommerce-LoopProduct-link  woocommerce-loop-product__link">';
}
add_action( 'woocommerce_before_shop_loop_item_title', 'fantasy_template_loop_image_link_open', 5 );


/**
 * Close link after the product thumbnail image
 *
 * @see get_the_permalink()
 */
function fantasy_template_loop_image_link_close() {
echo '</a>';
}
add_action( 'woocommerce_before_shop_loop_item_title', 'fantasy_template_loop_image_link_close', 30 );

add_action( 'woocommerce_shop_loop_item_title', 'fantasy_loop_product_content_header_open', 5 );

function fantasy_loop_product_content_header_open() {
echo '<div class="woocommerce-card__header">';
}

add_action( 'woocommerce_after_shop_loop_item', 'fantasy_loop_product_content_header_close', 60 );

function fantasy_loop_product_content_header_close() {
echo '</div>';
}

/**
 * Within Product Loop - remove title hook and create a new one with the category displayed above it.
 */
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'fantasy_loop_product_title', 10 );

function fantasy_loop_product_title() {

global $post;

?>
    <?php
    echo '<div class="woocommerce-loop-product__title"><a tabindex="0" href="' . get_the_permalink() . '" aria-label="' . get_the_title() . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">' . get_the_title() . '</a></div>';
}


/**
 * Display discounted % on product loop.
 */
add_action( 'woocommerce_before_shop_loop_item_title', 'fantasy_change_displayed_sale_price_html', 7 );
add_action( 'woocommerce_single_product_summary', 'fantasy_change_displayed_sale_price_html', 10 );
add_action( 'woocommerce_single_product_summary', 'fantasy_clear_product_price', 11 );

if ( ! function_exists( 'fantasy_clear_product_price' ) ) {
/**
 * Clear product price
 *
 * @since   1.0.0
 * @return  void
 */
function fantasy_clear_product_price() {
    echo '<div class="clear"></div>';
}
}

/**
 * Shop page - Out of Stock
 */
if ( ! function_exists( 'fantasy_shop_out_of_stock' ) ) :
/**
 * Add Out of Stock to the Shop page
 *
 * @hooked woocommerce_before_shop_loop_item_title - 8
 *
 * @since 1.8.5
 */
function fantasy_shop_out_of_stock() {
    $out_of_stock = get_post_meta(get_the_ID(), '_stock_status', true);
    $out_of_stock_string = apply_filters('fantasy_shop_out_of_stock_string', __('Out of stock', 'fantasy'));

    if ('outofstock' === $out_of_stock && !empty($out_of_stock_string)) {
        return '<span class="product-out-of-stock">' . esc_html($out_of_stock_string) . '</span>';
    }

    return '';
}


endif;

if (!function_exists('fantasy_safe_html')) {
    /**
     * Safely output HTML content.
     *
     * @param string $html The HTML content to be sanitized.
     * @return string The sanitized HTML content.
     */
    function fantasy_safe_html($html) {
        return wp_kses_post($html); // or use appropriate sanitization function
    }
}


function fantasy_change_displayed_sale_price_html() {
    global $product;
    $fantasy_sale_badge = '';

    if ($product->is_on_sale() && !$product->is_type('grouped') && !$product->is_type('bundle')) {
        if ($product->is_type('variable')) {
            $percentages = array();
            $prices = $product->get_variation_prices();
            foreach ($prices['price'] as $key => $price) {
                if ($prices['regular_price'][$key] !== $price && $prices['regular_price'][$key] > 0.005) {
                    $percentages[] = round(100 - ($prices['sale_price'][$key] / $prices['regular_price'][$key] * 100));
                }
            }
            if (!empty($percentages)) {
                $percentage = max($percentages) . '%';
            }
        } else {
            $percentage = 0;
            $regular_price = (float) $product->get_regular_price();
            if ($regular_price > 0.005) {
                $sale_price = (float) $product->get_price();
                $percentage = round(100 - ($sale_price / $regular_price * 100), 0) . '%';
            }
        }
        if (isset($percentage) && $percentage > 0) {
            $fantasy_sale_badge .= sprintf(__('<span class="sale-item product-label type-rounded">-%s</span>', 'fantasy'), $percentage);
        }
    }

    return fantasy_safe_html($fantasy_sale_badge);
}

function fantasy_product_badges() {
    global $product;

    // Capture the sale badge
    $sale_badge = fantasy_change_displayed_sale_price_html();

    // Capture the out of stock badge
    $out_of_stock_badge = fantasy_shop_out_of_stock();

    // Get the default WooCommerce sale flash
    ob_start();
    woocommerce_show_product_loop_sale_flash();
    $default_sale_flash = ob_get_clean();

    // Combine all badges
    $all_badges = $sale_badge . $out_of_stock_badge . $default_sale_flash;

    // Output badges inside a container
    if (!empty($all_badges)) {
        echo '<div class="products-badges">' . $all_badges . '</div>';
    }
}

// Hook into appropriate WooCommerce actions
add_action('woocommerce_before_shop_loop_item_title', 'fantasy_product_badges', 7);
add_action('woocommerce_single_product_summary', 'fantasy_product_badges', 10);


/**
 * Variation selected highlight
 *
 * @since 1.6.1
 */
add_action( 'woocommerce_before_add_to_cart_quantity', 'fantasy_highlight_selected_variation' );

function fantasy_highlight_selected_variation() {

global $product;

if ( $product->is_type( 'variable' ) ) {

    ?>
    <script>
document.addEventListener( 'DOMContentLoaded', function() {
var vari_labels = document.querySelectorAll('.variations .label label');
vari_labels.forEach( function( vari_label ) {
    vari_label.innerHTML = '<span>' + vari_label.innerHTML + '</span>';
} );

var vari_values = document.querySelectorAll('.value');
vari_values.forEach( function( vari_value ) {
    vari_value.addEventListener( 'change', function( event ) {
        var $this = event.target;
        if ( $this.selectedIndex != 0 ) {
            $this.closest( 'tr' ).classList.add( 'selected-variation' );
        } else {
            $this.closest( 'tr' ).classList.remove( 'selected-variation' );
        }
    } );
} );

document.addEventListener('click', function( event ){
    if ( event.target.classList.contains( 'reset_variations' ) ) {
        var vari_classs = document.querySelectorAll('.variations tr.selected-variation');
        vari_classs.forEach( function( vari_class ) {
            vari_class.classList.remove( 'selected-variation' );
        } );
    }
} );
} );
</script>
    <?php

}

}


/**
 * Single Product Page - Added to cart message.
 */
add_filter( 'wc_add_to_cart_message_html', 'fantasy_add_to_cart_message_filter', 10, 2 );

function fantasy_add_to_cart_message_filter( $message ) {

$fantasy_message = sprintf(
    '<div class="message-inner"><div class="message-content">%s </div><div class="buttons-wrapper"><a href="%s" class="button checkout"><span>%s</span></a> <a href="%s" class="button cart"><span>%s</span></a></div></div>',
    fantasy_safe_html( $message ),
    esc_url( wc_get_page_permalink( 'checkout' ) ),
    esc_html__( 'Checkout', 'fantasy' ),
    esc_url( wc_get_page_permalink( 'cart' ) ),
    esc_html__( 'View Cart', 'fantasy' )
	);

	return $fantasy_message;

}



/**
 * Cart wrapper open.
 */
function fantasy_cart_wrapper_open() {
	echo '<section class="fantasy-cart-wrapper">';
}

/**
 * Cart wrapper close.
 */

function fantasy_cart_wrapper_close() {
	echo '</section>';
}

add_action( 'woocommerce_before_cart', 'fantasy_cart_wrapper_open', 20 );
add_action( 'woocommerce_after_cart', 'fantasy_cart_wrapper_close', 10 );


/**
 * Add Progress Bar to the Cart and Checkout pages.
 */
add_action( 'woocommerce_before_cart', 'fantasy_cart_progress' );
add_action( 'woocommerce_before_checkout_form', 'fantasy_cart_progress', 5 );

if ( ! function_exists( 'fantasy_cart_progress' ) ) {

	/**
	 * More product info
	 * Link to product
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function fantasy_cart_progress() {



			?>

			<div class="checkout-wrap">
			<ul class="checkout-bar">
				<li class="active first"><span>
				<a href="<?php echo get_permalink( wc_get_page_id( 'cart' ) ); ?>"><?php esc_html_e( 'Shopping Cart', 'fantasy' ); ?></a></span>
				</li>
				<li class="next">
				<span>
				<a href="<?php echo get_permalink( wc_get_page_id( 'checkout' ) ); ?>"><?php esc_html_e( 'Shipping and Checkout', 'fantasy' ); ?></a></span></li>
				<li><span><?php esc_html_e( 'Confirmation', 'fantasy' ); ?></span></li>

			</ul>
			</div>
			<?php


		?>
		<?php

	}
}// End if().

/**
 * Single Product Page - Reorder sale message.
 */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_sale_flash', 3 );



/**
 * Appearance > Widgets > Custom Thank You Area. Loads at the bottom of the thank you page after an order has been placed.
 */
// add_action( 'woocommerce_thankyou', 'fantasy_custom_thankyou_section' );

// function fantasy_custom_thankyou_section() {
// 	echo '<div class="thankyou-custom-field">';
// 	dynamic_sidebar( 'thankyou-field' );
// 	echo '</div>';
// }


/**
* Remove "Description" heading from WooCommerce tabs.
*
* @since 1.0.0
*/
//add_filter( 'woocommerce_product_description_heading', '__return_null' );

// Change add to cart text on product archives page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives' );
function woocommerce_add_to_cart_button_text_archives() {
    return __( 'Add to cart', 'fantasy' );
}

// Change the Add to Cart button text on the single product page
add_filter('woocommerce_product_single_add_to_cart_text', 'custom_add_to_cart_text');
function custom_add_to_cart_text() {
    return __( 'Add to cart', 'fantasy' );
}


// Rename the coupon field on the cart page
add_filter( 'gettext', 'woocommerce_rename_coupon_field_on_cart', 10, 3 );
function woocommerce_rename_coupon_field_on_cart( $translated_text, $text, $domain ) {
    if ( $domain === 'woocommerce' ) {
        switch ( $text ) {
            case 'Apply coupon':
                $translated_text = __( 'Apply Coupon', 'fantasy' );
                break;
            case 'Coupon code':
                $translated_text = __( 'Enter Coupon Code', 'fantasy' );
                break;
        }
    }
    return $translated_text;
}


// Include the plugin.php file to use is_plugin_active if it's not already included
if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// Hook into the product listing loop to add the wishlist button
add_action('woocommerce_before_shop_loop_item_title', 'add_wishlist_button_to_product_listing', 10);

function add_wishlist_button_to_product_listing() {
    // Check if the wishlist plugin is active
    if (is_plugin_active('woo-smart-wishlist/wpc-smart-wishlist.php')) {
        global $product;
        $product_id = $product->get_id();

        // Generate the wishlist button shortcode only if the plugin is active
        if (function_exists('do_shortcode')) {
            $wishlist_button = do_shortcode('[woosw id="' . $product_id . '"]');

            // Output the wishlist button
            echo '<div class="wishlist-button-wrapper">' . $wishlist_button . '</div>';
        }
    }
}

// Remove default WooCommerce Add to Cart button
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
add_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 5);

function safe_exif_read_data($filename) {
    $exif = false;
    $size = getimagesize($filename, $info);

    if (function_exists('exif_read_data') && isset($info['APP1'])) {
        $app1_data = substr($info['APP1'], 0, 4);
        if ($app1_data === 'Exif') {
            try {
                $exif = @exif_read_data($filename);
            } catch (Exception $e) {
                error_log('EXIF read data error: ' . $e->getMessage());
            }
        }
    }

    return $exif;
}


// Add second product image on hover with lazy loading
add_action('woocommerce_before_shop_loop_item_title', 'fantasy_show_second_image_on_hover_new', 20);

function fantasy_show_second_image_on_hover_new() {
    global $product;

    // Get the product gallery attachment ids
    $attachment_ids = $product->get_gallery_image_ids();

    if ($attachment_ids) {
        $secondary_image_id = $attachment_ids[0]; // Get the first image in the gallery
        $secondary_image_url = wp_get_attachment_image_src($secondary_image_id, 'woocommerce_thumbnail')[0];

        if ($secondary_image_url) {
            echo '<img src="' . esc_url($secondary_image_url) . '" class="secondary-image attachment-shop-catalog" alt="' . esc_attr($product->get_name()) . '" loading="lazy">';
        }
    }
}

// deactivate new block editor
function phi_theme_support() {
    remove_theme_support( 'widgets-block-editor' );
}
add_action( 'after_setup_theme', 'phi_theme_support' );

// Remove default Add to Cart button for variable products in related and cross-sells sections
function remove_variable_add_to_cart_button_related_cross_sells() {
    global $product;
    if ($product->is_type('variable')) {
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    }
}
add_action('woocommerce_after_shop_loop_item', 'remove_variable_add_to_cart_button_related_cross_sells', 1);

// Remove default Add to Cart button for variable products
add_action('woocommerce_after_shop_loop_item', 'remove_variable_add_to_cart_button', 1);
function remove_variable_add_to_cart_button() {
    global $product;
    if ($product->is_type('variable')) {
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    }
}

// Add Quick View button to product loop
function add_quick_view_button() {
    global $product;
    if ($product->is_type('variable')) {
        echo '<button class="button open-quick-view" data-product-id="' . esc_attr($product->get_id()) . '">' . __('Quick View', 'fantasy') . '</button>';
    }
}
add_action('woocommerce_shop_loop_item_title', 'add_quick_view_button', 5);

function enqueue_woocommerce_ajax_add_to_cart_script() {
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-add-to-cart-variation');
        wp_enqueue_script('wc-cart-fragments');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_woocommerce_ajax_add_to_cart_script');



// Enqueue Quick View Script
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('quick-view-js', get_theme_file_uri('resources/scripts/quick-view.js'), ['jquery'], null, true);
    wp_localize_script('quick-view-js', 'quickViewAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('woosq-security'),
    ]);
});

function quick_view_load_product() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'woosq-security')) {
        wp_send_json_error('Invalid nonce');
    }

    $product_id = absint($_POST['product_id']);
    $product = wc_get_product($product_id);

    if (!$product) {
        wp_send_json_error('Invalid product');
    }

    // Get the post object
    $post = get_post($product_id);

    if (!$post) {
        wp_send_json_error('Invalid post');
    }

    // Set up product data
    setup_postdata($post);

    // Output product content using Blade template
    ob_start();
    echo view('woocommerce.content-quick-view-variable', compact('product'))->render();
    $output = ob_get_clean();

    wp_send_json_success($output);

    wp_die();
}
add_action('wp_ajax_woosq_quickview', 'quick_view_load_product');
add_action('wp_ajax_nopriv_woosq_quickview', 'quick_view_load_product');

function quick_view_add_to_cart() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'woosq-security')) {
        wp_send_json_error('Invalid nonce');
    }

    $product_id = intval($_POST['add-to-cart']);
    $quantity = isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : 1;
    $variation_id = intval($_POST['variation_id']);
    $variation = !empty($_POST['variation']) ? $_POST['variation'] : [];

    $product_status = get_post_status($product_id);

    if ($product_status !== 'publish') {
        wp_send_json_error(['error' => true, 'product_url' => get_permalink($product_id)]);
        return;
    }

    $cart_item_data = [];

    if (WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $cart_item_data)) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message([$product_id => $quantity], true);
        }

        WC_AJAX::get_refreshed_fragments();
    } else {
        wp_send_json_error(['error' => true, 'product_url' => get_permalink($product_id)]);
    }

    wp_die();
}
add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'quick_view_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'quick_view_add_to_cart');


remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');



function enqueue_woocommerce_scripts() {
    if (class_exists('WooCommerce')) {
        // Enqueue WooCommerce scripts

        wp_enqueue_script('wc-single-product');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_woocommerce_scripts');

// Add additional logging to functions.php
add_action('wp_ajax_log_to_debug', 'log_to_debug');
add_action('wp_ajax_nopriv_log_to_debug', 'log_to_debug');

function log_to_debug() {
    if (isset($_POST['message'])) {
        $message = sanitize_text_field($_POST['message']);
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log($message);
        }
    }
    wp_die();
}

// Log quantity change
add_filter('woocommerce_add_to_cart_quantity', 'log_quantity_change', 10, 2);
function log_quantity_change($quantity, $product_id) {
    error_log("Product ID: $product_id, Quantity being added: $quantity");
    return $quantity;
}

// Log cart contents
add_action('woocommerce_add_to_cart', 'log_cart_contents', 10, 6);
function log_cart_contents($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
    $cart = WC()->cart->get_cart();
    $total_quantity = 0;
    foreach ($cart as $item) {
        $total_quantity += $item['quantity'];
    }
    error_log("Total Cart Quantity after adding: $total_quantity");
}

