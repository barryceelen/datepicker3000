<?php
/**
 * WordPress jQuery UI Datepicker example plugin
 *
 * @package   Datepicker3000
 * @author    Barry Ceelen <b@rryceelen.com>
 * @license   GPL-3.0+
 * @link      https://github.com/barryceelen/datepicker3000
 * @copyright 2016 Barry Ceelen
 *
 * Plugin Name: Datepicker3000
 * Description: jQuery UI Datepicker example plugin. Adds a meta box with  date input field to the post edit screen. See: <a href="https://core.trac.wordpress.org/ticket/29420">https://core.trac.wordpress.org/ticket/29420</a>
 * Version:     1.0.1
 * Author:      Barry Ceelen
 * Author URI:  https://github.com/barryceelen
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_action( 'admin_enqueue_scripts', 'datepicker3000_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'datepicker3000_enqueue_styles' );
add_action( 'init', 'datepicker3000_add_post_type_support' );
add_action( 'add_meta_boxes', 'datepicker3000_add_meta_box' );
add_action( 'save_post', 'datepicker3000_save_meta_box', 10, 2 );

/**
 * Add support for the datepicker to the 'post' post type by default.
 *
 * @since 1.0.1
 */
function datepicker3000_add_post_type_support() {

	/**
	 * Filters the list of supported post types
	 *
	 * @since 1.0.1
	 *
	 * @param array An array of supported post types.
	 */
	$supported_post_types = apply_filters( 'datepicker3000_supported_post_types', array( 'post' ) );

	foreach( $supported_post_types as $post_type ) {
		add_post_type_support( $post_type, 'datepicker3000' );
	}
}

/**
 * Register meta box.
 *
 * @since 1.0.0
 *
 * @param WP_Post $post The post in question.
 */
function datepicker3000_add_meta_box( $post ) {

	add_meta_box(
		'datepicker',
		__( 'Date', 'datepicker' ),
		'datepicker3000_meta_box',
		get_post_types_by_support( 'datepicker3000' ),
		'normal',
		'high'
	);
}

/**
 * Meta box content.
 *
 * @since 1.0.0
 */
function datepicker3000_meta_box() {

	global $post;

	$date         = empty( $post->_date ) ? current_time( 'Y-m-d' ): $post->_date;
	$date_display = mysql2date( get_option( 'date_format' ), $date );

	wp_nonce_field( plugin_basename( __FILE__ ), 'datepicker3000_' . $post->ID );

	printf(
		'<input type="text" name="datepicker3000" style="background:#fff;cursor:pointer;" value="%s" readonly="readonly" /><input type="hidden" name="datepicker3000-alt" value="%s" /> <span class="description hide-if-js">%s</span>',
		esc_attr( $date_display ),
		esc_attr( $date ),
		__( 'JavaScript must be enabled to use this feature.' )
	);
}

/**
 * Save the date meta box value.
 *
 * @since 1.0.1
 *
 * @param int     $post_id ID of the post in question.
 * @param WP_Post $post    Post object.
 */
function datepicker3000_save_meta_box( $post_id, $post ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! in_array( $post->post_type, get_post_types_by_support( 'datepicker3000' ) ) ) {
		return;
	}

	if ( ! isset( $_POST[ 'datepicker3000_' . $post_id ] ) || ! wp_verify_nonce( $_POST[ 'datepicker3000_' . $post_id ], plugin_basename( __FILE__ ) ) ) {
		return;
	}

	$post_type = get_post_type_object( $post->post_type );

	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return;
	}

	if ( isset( $_POST['datepicker3000-alt'] ) ) {
		update_post_meta( $post_id, '_date', trim( $_POST['datepicker3000-alt'] ) );
	}
}

/**
 * Enqueue JavaScript.
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix The current admin page.
 */
function datepicker3000_enqueue_scripts( $hook_suffix ) {

	if ( in_array( $hook_suffix, array( 'post-new.php', 'post.php' ), true ) ) {

		wp_enqueue_script(
			'datepicker3000',
			plugins_url( '/js/admin.js' , __FILE__ ),
			array( 'jquery-ui-datepicker' ),
			null,
			true
		);
	}
}

/**
 * Enqueue our own base jQuery UI base styles and datepicker skin.
 *
 * See: https://core.trac.wordpress.org/ticket/18909
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix The current admin page.
 */
function datepicker3000_enqueue_styles( $hook_suffix ) {

	if ( in_array( $hook_suffix, array( 'post-new.php', 'post.php' ), true ) ) {

		// Base styles needed for the datepicker skin by XWP.
		wp_enqueue_style(
			'datepicker3000-jquery-ui',
			plugins_url( '/css/jquery-ui.min.css', __FILE__ ),
			array(),
			null
		);

		// Datepicker skin by XWP, https://github.com/xwp/wp-jquery-ui-datepicker-skins.
		wp_enqueue_style(
			'datepicker3000-jquery-ui-datepicker',
			plugins_url( '/css/datepicker.css', __FILE__ ),
			array( 'datepicker3000-jquery-ui' ),
			null
		);
	}
}
