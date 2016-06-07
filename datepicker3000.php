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
 * Version:     1.0.0
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
add_action( 'add_meta_boxes', 'datepicker3000_add_meta_box' );

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
		'post',
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

	echo '<input type="text" name="datepicker3000" />';
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
