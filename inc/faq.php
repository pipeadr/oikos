<?php
/**
 * Preguntas frecuentes: lectura de datos y helper de la sección.
 *
 * Cada página tiene sus propias preguntas (grupo ACF "Preguntas frecuentes",
 * acf-json/group_oikos_faq.json). La plantilla de la sección
 * (template-parts/sections/faq.php) y el schema FAQPage (inc/schema.php)
 * usan las mismas preguntas, y el schema solo sale si la sección se renderizó
 * (ver oikos_prepare_faq_section()).
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

/* ==========================================================================
   Sección renderizada = única fuente del schema y de los assets
   ==========================================================================

   El schema FAQPage sale en el <head> (wp_head), antes de que la plantilla
   llegue a la sección. Para que dependa de que la sección exista de verdad
   (y no solo de que el campo tenga datos), la plantilla la renderiza ANTES de
   get_header() y la imprime después en su sitio:

       oikos_prepare_faq_section( 'faq-servicios', $page_id ); // antes de get_header()
       get_header();
       …
       oikos_the_faq_section();                                // donde va la sección

   Una plantilla que no la prepara (page.php, home.php hoy) no tiene FAQPage
   ni carga faq.css/faq.js, aunque la página tenga preguntas cargadas. */

/**
 * Estado de la sección de la vista actual (por referencia).
 *
 * @return array{prepared:bool, printed:bool, html:string, items:array}
 */
function &oikos_faq_state() {
	static $state = array(
		'prepared' => false,
		'printed'  => false,
		'html'     => '',
		'items'    => array(),
	);

	return $state;
}

/**
 * Renderiza la sección de preguntas frecuentes y la guarda para imprimirla
 * después. Llamar en la plantilla antes de get_header().
 *
 * @param string $track_location data-track-location de los botones ("faq-<pagina>").
 * @param int    $post_id        Página de la que se leen las preguntas (0 = la de la vista).
 * @return bool true si la sección produjo HTML.
 */
function oikos_prepare_faq_section( $track_location, $post_id = 0 ) {
	$state   = &oikos_faq_state();
	$post_id = $post_id ? absint( $post_id ) : oikos_faq_post_id();
	$items   = oikos_get_faq_items( $post_id );
	$html    = '';

	if ( $items ) {
		ob_start();
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
		$html = trim( (string) ob_get_clean() );
	}

	$state = array(
		'prepared' => true,
		'printed'  => false,
		'html'     => $html,
		'items'    => '' !== $html ? $items : array(),
	);

	return '' !== $html;
}

/**
 * Imprime la sección preparada con oikos_prepare_faq_section().
 *
 * @return void
 */
function oikos_the_faq_section() {
	$state = &oikos_faq_state();

	if ( ! $state['prepared'] ) {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Llama a oikos_prepare_faq_section() antes de get_header(): el schema FAQPage depende de la sección ya renderizada.', 'oikos' ), '' );
		return;
	}

	if ( $state['printed'] ) {
		return;
	}

	$state['printed'] = true;
	echo $state['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- salida de template-parts/sections/faq.php, ya escapada.
}

/**
 * Preguntas de la sección realmente renderizada en la vista actual
 * (las que van al schema). Vacío si la plantilla no la preparó o no produjo HTML.
 *
 * @return array<int, array{question:string, answer:string}>
 */
function oikos_faq_rendered_items() {
	$state = &oikos_faq_state();

	return $state['items'];
}

/**
 * ¿La vista actual muestra la sección de preguntas? (condición de assets y schema).
 *
 * @return bool
 */
function oikos_page_has_faq() {
	return (bool) oikos_faq_rendered_items();
}

/**
 * Red de seguridad: sección preparada (con schema en el <head>) que la
 * plantilla nunca imprimió. Aviso visible con WP_DEBUG.
 *
 * @return void
 */
function oikos_faq_check_printed() {
	$state = &oikos_faq_state();

	if ( $state['items'] && ! $state['printed'] ) {
		_doing_it_wrong( 'oikos_prepare_faq_section', esc_html__( 'La sección de preguntas se preparó (y generó FAQPage) pero la plantilla no llamó a oikos_the_faq_section().', 'oikos' ), '' );
	}
}
add_action( 'wp_footer', 'oikos_faq_check_printed' );
