<?php
/**
 * Shoptimizer functions.
 *
 * @package shoptimizer
 */

if ( ! function_exists( 'shoptimizer_is_woocommerce_activated' ) ) {
	/**
	 * Query WooCommerce activation
	 */
	function shoptimizer_is_woocommerce_activated() {
		return class_exists( 'WooCommerce' ) ? true : false;
	}
}



/**
 * Produces nice safe html for presentation.
 *
 * @param $input - accepts a string.
 * @return string
 */
function fantasy_safe_html( $input ) {

	$args = array(
		// formatting.
		'span'   => array(
			'class' => array(),
		),
		'h1'     => array(
			'class' => array(),
		),
		'h2'     => array(
			'class' => array(),
		),
		'h3'     => array(
			'class' => array(),
		),
		'h4'     => array(
			'class' => array(),
		),
		'del'    => array(),
		'ins'    => array(),
		'strong' => array(),
		'em'     => array(),
		'b'      => array(),
		'hr'     => array(),
		'i'      => array(
			'class' => array(),
		),
		'img'      => array(
			'href'        => array(),
			'alt'         => array(),
			'class'       => array(),
			'scale'       => array(),
			'width'       => array(),
			'height'      => array(),
			'src'         => array(),
			'srcset'      => array(),
			'sizes'       => array(),
			'data-src'    => array(),
			'data-srcset' => array(),
		),
		'p'     => array(
			'class' => array(),
		),
		'figure'     => array(
			'class' => array(),
		),
		'div'     => array(
			'class' => array(),
			'style' => array(),
		),
		'ul'     => array(
			'class' => array(),
		),
		'li'     => array(
			'class' => array(),
		),
		'mark'   => array(
			'class' => array(),
		),

		// links.
		'a'        => array(
			'href'            => array(),
			'data-product-id' => array(),
			'data-type'       => array(),
			'data-wpage'      => array(),
			'class'           => array(),
			'aria-label'      => array(),
			'target'          => array(),
		),
	);

	return wp_kses( $input, $args );
}
