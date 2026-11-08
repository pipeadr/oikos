<?php
/**
 * Helpers del tema.
 *
 * Envoltorios seguros para que el tema no se rompa si ACF o Yoast
 * están desactivados, y utilidades de rutas de assets.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Devuelve el valor de un campo ACF o un valor por defecto si ACF no está activo.
 *
 * @param string     $name    Nombre del campo.
 * @param int|string $post_id ID del post (o 'option'). false = post actual.
 * @param mixed      $default Valor si el campo está vacío o ACF no existe.
 * @return mixed
 */
function oikos_field( $name, $post_id = false, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $name, $post_id );

	return ( null === $value || '' === $value || false === $value ) ? $default : $value;
}

/**
 * Devuelve un campo de la página de opciones "Oikos — Global".
 *
 * @param string $name    Nombre del campo.
 * @param mixed  $default Valor por defecto.
 * @return mixed
 */
function oikos_option( $name, $default = '' ) {
	return oikos_field( $name, 'option', $default );
}

/**
 * Imprime las migas de pan de Yoast si el plugin está activo.
 *
 * @param string $before HTML antes de las migas.
 * @param string $after  HTML después de las migas.
 * @return void
 */
function oikos_breadcrumbs( $before = '<nav class="oikos-breadcrumbs" aria-label="Breadcrumb">', $after = '</nav>' ) {
	if ( ! function_exists( 'yoast_breadcrumb' ) ) {
		return;
	}

	yoast_breadcrumb( $before, $after );
}

/**
 * URL de un asset del tema.
 *
 * @param string $path Ruta relativa dentro de /assets (ej. 'css/base.css').
 * @return string
 */
function oikos_asset_uri( $path ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

/**
 * Ruta absoluta en disco de un asset del tema.
 *
 * @param string $path Ruta relativa dentro de /assets.
 * @return string
 */
function oikos_asset_path( $path ) {
	return get_template_directory() . '/assets/' . ltrim( $path, '/' );
}

/**
 * Versión de un asset basada en su fecha de modificación (cache-busting).
 *
 * @param string $path Ruta relativa dentro de /assets.
 * @return string|null
 */
function oikos_asset_version( $path ) {
	$file = oikos_asset_path( $path );

	return file_exists( $file ) ? (string) filemtime( $file ) : null;
}
