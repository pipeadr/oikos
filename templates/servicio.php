<?php
/**
 * Template Name: Servicio
 *
 * Plantilla compartida de las páginas de terapia (pareja, individual,
 * familiar). Secciones administrables desde el grupo ACF "Servicio"
 * (acf-json/group_oikos_servicio.json), una pestaña por sección. La tarjeta
 * del carrusel de servicios sigue en el grupo "Tarjeta de servicio".
 *
 * Hero a sangre: el header flota sobre la foto (oikos_has_hero).
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_page_id = get_queried_object_id();
$oikos_slug    = (string) get_post_field( 'post_name', $oikos_page_id );

// Hero a sangre: el header flota encima y el <main> no lleva padding superior.
add_filter( 'oikos_has_hero', '__return_true' );

// Preguntas frecuentes + bloque WhatsApp: se renderizan antes del <head> para
// que el schema FAQPage dependa de la sección real. Se imprimen al final.
oikos_prepare_faq_section( 'faq-' . $oikos_slug, $oikos_page_id );

get_header();

// Hero: foto B/N de fondo con texto a la derecha. Título vacío → título de la
// página; botón vacío → CTA global.
get_template_part(
	'template-parts/sections/hero-backdrop',
	null,
	array(
		'image_id'       => (int) oikos_field( 'oikos_servicio_hero_imagen', $oikos_page_id, 0 ),
		'focus_x'        => (int) oikos_field( 'oikos_servicio_hero_foco_x', $oikos_page_id, 50 ),
		'focus_y'        => (int) oikos_field( 'oikos_servicio_hero_foco_y', $oikos_page_id, 50 ),
		'title'          => (string) oikos_field( 'oikos_servicio_hero_titulo', $oikos_page_id, get_the_title( $oikos_page_id ) ),
		'text'           => (string) oikos_field( 'oikos_servicio_hero_texto', $oikos_page_id ),
		'link'           => oikos_link_or_cta( oikos_field( 'oikos_servicio_hero_boton', $oikos_page_id, array() ) ),
		'track_location' => 'servicio-hero-' . $oikos_slug,
	)
);

// Temas que aborda la terapia: grilla de 3 columnas. Título vacío → sin sección.
$oikos_topics = array();
foreach ( (array) oikos_field( 'oikos_servicio_temas_lista', $oikos_page_id, array() ) as $oikos_row ) {
	$oikos_topics[] = array(
		'title' => $oikos_row['titulo'] ?? '',
		'text'  => $oikos_row['descripcion'] ?? '',
		'link'  => $oikos_row['enlace'] ?? array(),
	);
}

get_template_part(
	'template-parts/sections/topics',
	null,
	array(
		'id'             => 'temas',
		'title'          => (string) oikos_field( 'oikos_servicio_temas_titulo', $oikos_page_id ),
		'text'           => (string) oikos_field( 'oikos_servicio_temas_texto', $oikos_page_id ),
		'items'          => $oikos_topics,
		'columns'        => 3,
		'track_location' => 'temas-' . $oikos_slug,
	)
);

// Preguntas frecuentes + bloque WhatsApp (preparadas antes de get_header()).
oikos_the_faq_section();

get_footer();
