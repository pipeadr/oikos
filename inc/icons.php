<?php
/**
 * Íconos SVG en línea.
 *
 * Los SVG viven en assets/img/icons/<nombre>.svg (24×24, currentColor) y se
 * imprimen en línea para heredar el color del texto y no generar peticiones.
 * Siempre son decorativos: el nombre accesible lo da el enlace o botón.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Devuelve el markup de un ícono.
 *
 * @param string $name  Nombre del archivo sin extensión (ej. 'whatsapp').
 * @param string $class Clases CSS adicionales.
 * @return string SVG listo para imprimir, o '' si no existe.
 */
function oikos_icon( $name, $class = '' ) {
	static $cache = array();

	$name = sanitize_key( $name );

	if ( ! isset( $cache[ $name ] ) ) {
		$file           = oikos_asset_path( 'img/icons/' . $name . '.svg' );
		$cache[ $name ] = is_readable( $file ) ? trim( (string) file_get_contents( $file ) ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local del tema.
	}

	if ( '' === $cache[ $name ] ) {
		return '';
	}

	$classes = trim( 'oikos-icon oikos-icon--' . $name . ' ' . $class );

	return preg_replace(
		'/^<svg\b/',
		sprintf( '<svg class="%s" aria-hidden="true" focusable="false" width="24" height="24"', esc_attr( $classes ) ),
		$cache[ $name ],
		1
	);
}

/**
 * Lista blanca de etiquetas y atributos SVG permitidos al imprimir íconos.
 *
 * @return array
 */
function oikos_icon_kses() {
	$shape = array(
		'd'               => true,
		'fill'            => true,
		'stroke'          => true,
		'stroke-width'    => true,
		'stroke-linecap'  => true,
		'stroke-linejoin' => true,
		'x'               => true,
		'y'               => true,
		'width'           => true,
		'height'          => true,
		'rx'              => true,
		'cx'              => true,
		'cy'              => true,
		'r'               => true,
	);

	return array(
		'svg'    => array_merge(
			$shape,
			array(
				'xmlns'       => true,
				'viewbox'     => true,
				'class'       => true,
				'aria-hidden' => true,
				'focusable'   => true,
			)
		),
		'path'   => $shape,
		'rect'   => $shape,
		'circle' => $shape,
	);
}

/**
 * Imprime un ícono (escapado con la lista blanca SVG).
 *
 * @param string $name  Nombre del ícono.
 * @param string $class Clases CSS adicionales.
 * @return void
 */
function oikos_the_icon( $name, $class = '' ) {
	echo wp_kses( oikos_icon( $name, $class ), oikos_icon_kses() );
}

/**
 * Devuelve el markup de una ilustración decorativa (líneas orgánicas, etc.).
 *
 * Los SVG viven en assets/img/<nombre>.svg y se imprimen en línea. Se les
 * quitan width/height (el tamaño lo da el CSS; el viewBox conserva la
 * proporción) y siempre son decorativos: aria-hidden y fuera del foco.
 *
 * @param string $name  Nombre del archivo sin extensión (ej. 'decor-lineas').
 * @param string $class Clases CSS adicionales.
 * @return string SVG listo para imprimir, o '' si no existe.
 */
function oikos_decor( $name, $class = '' ) {
	static $cache = array();

	$name = sanitize_file_name( $name );

	if ( ! isset( $cache[ $name ] ) ) {
		$file           = oikos_asset_path( 'img/' . $name . '.svg' );
		$cache[ $name ] = is_readable( $file ) ? trim( (string) file_get_contents( $file ) ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local del tema.
	}

	if ( '' === $cache[ $name ] ) {
		return '';
	}

	$classes = trim( 'oikos-decor ' . $class );

	return (string) preg_replace_callback(
		'/^<svg\b([^>]*)>/',
		static function ( $match ) use ( $classes ) {
			$attrs = preg_replace( '/\s(?:width|height|class)="[^"]*"/', '', $match[1] );

			return sprintf( '<svg class="%s" aria-hidden="true" focusable="false" preserveAspectRatio="xMidYMid meet"%s>', esc_attr( $classes ), $attrs );
		},
		$cache[ $name ],
		1
	);
}

/**
 * Imprime una ilustración decorativa (escapada con la lista blanca SVG).
 *
 * @param string $name  Nombre del archivo sin extensión.
 * @param string $class Clases CSS adicionales.
 * @return void
 */
function oikos_the_decor( $name, $class = '' ) {
	$allowed = oikos_icon_kses();

	$allowed['svg']['preserveaspectratio'] = true;
	$allowed['path']['opacity']            = true;
	$allowed['path']['fill-rule']          = true;
	$allowed['path']['clip-rule']          = true;

	echo wp_kses( oikos_decor( $name, $class ), $allowed );
}
