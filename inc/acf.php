<?php
/**
 * Integración con ACF Pro: Local JSON y páginas de opciones.
 *
 * Si ACF está desactivado, estos hooks nunca se disparan y las
 * funciones de ACF no se llaman (guardas con function_exists).
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Guarda los grupos de campos en /acf-json del tema.
 *
 * @return string
 */
function oikos_acf_json_save_path() {
	return get_template_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'oikos_acf_json_save_path' );

/**
 * Carga los grupos de campos desde /acf-json (reemplaza la ruta por defecto).
 *
 * @param array $paths Rutas de carga.
 * @return array
 */
function oikos_acf_json_load_paths( $paths ) {
	unset( $paths[0] );
	$paths[] = get_template_directory() . '/acf-json';

	return $paths;
}
add_filter( 'acf/settings/load_json', 'oikos_acf_json_load_paths' );

/**
 * Registra la página de opciones "Oikos — Global".
 *
 * @return void
 */
function oikos_acf_options_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Oikos — Global', 'oikos' ),
			'menu_title' => __( 'Oikos — Global', 'oikos' ),
			'menu_slug'  => 'oikos-global',
			'capability' => 'manage_options',
			'icon_url'   => 'dashicons-admin-site-alt3',
			'position'   => 59,
			'redirect'   => false,
		)
	);
}
add_action( 'acf/init', 'oikos_acf_options_pages' );
