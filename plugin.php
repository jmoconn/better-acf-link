<?php
/**
 * @package  tm_link
 */
/*
Plugin Name: Teal Media Custom Link
Description: Adds a better ACF link field
Author: Teal Media
Plugin URI: https://tealmedia.com
Text Domain: tm_link
Version: 1.0
 */

/*
 * Bail early if accessed directly
 */
defined( 'ABSPATH' ) or die();

/*
 * Define constants
 */
define( 'TM_LINK_PATH', plugin_dir_path( __FILE__ ) );
define( 'TM_LINK_URL', plugin_dir_url( __FILE__ ) );

/*
 * Initialize the core classes of the plugin
 */
function tm_initialize_custom_link()
{
	include_once TM_LINK_PATH . 'class-tm-link.php';

	// enqueue scripts and styles
	add_action( 'admin_enqueue_scripts', 'tm_enqueue_custom_link_scripts' );
}

add_action( 'after_setup_theme', 'tm_initialize_custom_link' );

function tm_enqueue_custom_link_scripts()
{
	wp_enqueue_script( 'tm-link', TM_LINK_URL . 'tm-link.js', [], '1.0', true );
	wp_enqueue_style( 'tm-link', TM_LINK_URL . 'tm-link.css', [], '1.0' );
}

if ( !function_exists( 'tm_get_custom_link' ) ) {
	function tm_get_custom_link( $link )
	{
		$defaults = [
			'type' => null,
			'value' => null,
			'url' => null,
			'name' => null,
			'title' => null,
			'target' => null,
			'post_types' => [],
			'label' => null,
			'relationship' => null
		];

		$link = wp_parse_args( $link, $defaults );

		$label = $link['label'] ?: $link['name'] ?: $link['title'] ?: $link['url'];

		return sprintf( '<a href="%s" title="%s" target="%s" rel="%s">%s</a>',
			esc_url( $link['url'] ),
			esc_attr( $link['title'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['relationship'] ),
			esc_html( $label )
		);
	}
}
if ( !function_exists( 'tm_print_custom_link' ) ) {
	function tm_print_custom_link( $link )
	{
		print tm_get_custom_link( $link );
	}
}

if ( !function_exists( 'tm_print_link_open' ) ) {
	function tm_print_link_open( $link )
	{
		$defaults = [
			'type' => null,
			'value' => null,
			'url' => null,
			'name' => null,
			'title' => null,
			'target' => null,
			'post_types' => [],
			'label' => null,
			'relationship' => null
		];

		$link = wp_parse_args( $link, $defaults );

		return sprintf( '<a href="%s" title="%s" target="%s" rel="%s">',
			esc_url( $link['url'] ),
			esc_attr( $link['title'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['relationship'] ),
		);
	}
}

if ( !function_exists( 'tm_print_link_close' ) ) {
	function tm_print_link_close()
	{
		print '</a>';
	}
}