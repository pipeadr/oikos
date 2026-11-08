<?php
/**
 * Configuración base del tema: soportes, menús y limpieza de wp_head.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra los soportes del tema y las ubicaciones de menú.
 *
 * @return void
 */
function oikos_setup() {
	load_theme_textdomain( 'oikos', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary'                => __( 'Menú principal', 'oikos' ),
			'footer-acompanamientos' => __( 'Footer — Acompañamientos', 'oikos' ),
			'footer-contenido'       => __( 'Footer — Contenido', 'oikos' ),
			'footer-oikos'           => __( 'Footer — Oikos', 'oikos' ),
		)
	);
}
add_action( 'after_setup_theme', 'oikos_setup' );

/**
 * Limpia etiquetas innecesarias de wp_head.
 *
 * @return void
 */
function oikos_cleanup_head() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
}
add_action( 'init', 'oikos_cleanup_head' );
