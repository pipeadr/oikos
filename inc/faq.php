<?php
/**
 * Preguntas frecuentes: lectura de datos y helper de la sección.
 *
 * Cada página tiene sus propias preguntas (grupo ACF "Preguntas frecuentes",
 * acf-json/group_oikos_faq.json). La plantilla de la sección
 * (template-parts/sections/faq.php) y el schema FAQPage (inc/schema.php)
 * leen las mismas preguntas con oikos_get_faq_items(), así nunca difieren.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * ID de la página cuyas preguntas corresponden a la vista actual.
 *
 * - Página de entradas (Blog): la página asignada en Ajustes → Lectura,
 *   solo en la primera página del listado.
 * - Cualquier vista singular: el objeto consultado.
 *
 * @return int 0 si la vista no tiene página asociada.
 */
function oikos_faq_post_id() {
	if ( is_home() && ! is_front_page() ) {
		return is_paged() ? 0 : (int) get_option( 'page_for_posts' );
	}

	return is_singular() ? (int) get_queried_object_id() : 0;
}

/**
 * Normaliza el HTML de una respuesta (editor wysiwyg básico).
 *
 * El editor (con wpautop de ACF) ya entrega <p>, <ul> y <ol>; aquí se
 * garantiza que el resultado siempre sean bloques limpios:
 * - sin párrafos vacíos (<p></p>, <p>&nbsp;</p>, <p><br></p>);
 * - texto suelto (sin bloques) → envuelto en <p> con wpautop();
 * - sin <p> envolviendo listas ni otros bloques.
 *
 * @param string $html HTML de la respuesta.
 * @return string
 */
function oikos_faq_clean_answer( $html ) {
	$html  = trim( (string) $html );
	$block = 'p|ul|ol|li|h[2-6]|blockquote|div';

	if ( '' === $html ) {
		return '';
	}

	if ( ! preg_match( '/<(' . $block . ')[\s>]/i', $html ) ) {
		$html = wpautop( $html );
	}

	// <p> alrededor de un bloque: se deja solo el bloque.
	$html = preg_replace( '#<p>\s*(<(?:ul|ol|h[2-6]|blockquote|div)[\s>].*?</(?:ul|ol|h[2-6]|blockquote|div)>)\s*</p>#is', '$1', $html );

	// Párrafos vacíos.
	$html = preg_replace( '#<p[^>]*>(?:\s|&nbsp;|&\#160;|\xC2\xA0|<br\s*/?>)*</p>#i', '', $html );

	return trim( $html );
}

/**
 * Preguntas de una página, normalizadas.
 *
 * Descarta filas sin pregunta o sin respuesta.
 *
 * @param int $post_id ID de la página.
 * @return array<int, array{question:string, answer:string}> answer = HTML del editor.
 */
function oikos_get_faq_items( $post_id ) {
	static $cache = array();

	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return array();
	}

	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	$items = array();
	foreach ( (array) oikos_field( 'oikos_faq_preguntas', $post_id, array() ) as $row ) {
		$question = isset( $row['pregunta'] ) ? trim( wp_strip_all_tags( (string) $row['pregunta'] ) ) : '';
		$answer   = isset( $row['respuesta'] ) ? oikos_faq_clean_answer( (string) $row['respuesta'] ) : '';

		if ( '' === $question || '' === trim( wp_strip_all_tags( $answer ) ) ) {
			continue;
		}

		$items[] = array(
			'question' => $question,
			'answer'   => $answer,
		);
	}

	$cache[ $post_id ] = $items;

	return $items;
}

/**
 * ¿La vista actual tiene preguntas frecuentes? (condición de assets y schema).
 *
 * @return bool
 */
function oikos_page_has_faq() {
	return (bool) oikos_get_faq_items( oikos_faq_post_id() );
}

/**
 * Imprime la sección de preguntas frecuentes de una página.
 *
 * Uso en una plantilla: oikos_the_faq_section( 'faq-contacto' );
 *
 * @param string $track_location data-track-location de los botones ("faq-<pagina>").
 * @param int    $post_id        Página de la que se leen las preguntas (0 = la actual).
 * @return void
 */
function oikos_the_faq_section( $track_location, $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : oikos_faq_post_id();
	$items   = oikos_get_faq_items( $post_id );

	if ( ! $items ) {
		return;
	}

	get_template_part(
		'template-parts/sections/faq',
		null,
		array(
			'title'          => (string) oikos_field( 'oikos_faq_titulo', $post_id, __( 'Preguntas frecuentes', 'oikos' ) ),
			'heading_level'  => 2,
			'items'          => $items,
			'link'           => oikos_link_or_cta( oikos_field( 'oikos_faq_boton', $post_id, array() ) ),
			'track_location' => (string) $track_location,
		)
	);
}
