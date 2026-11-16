<?php
/**
 * Template Name: Servicios general
 *
 * Página general de servicios (/servicios/).
 * Secciones administrables desde el grupo ACF "Servicios general"
 * (acf-json/group_oikos_servicios.json), una pestaña por sección.
 *
 * El header no flota sobre el hero: el <main> conserva su padding superior.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_page_id = get_queried_object_id();

get_header();

// Hero foto + texto. Título vacío → título de la página; botón vacío → CTA global.
get_template_part(
	'template-parts/sections/hero-split',
	null,
	array(
		'image_id'       => (int) oikos_field( 'oikos_servicios_hero_imagen', $oikos_page_id, 0 ),
		'title'          => (string) oikos_field( 'oikos_servicios_hero_titulo', $oikos_page_id, get_the_title( $oikos_page_id ) ),
		'text'           => (string) oikos_field( 'oikos_servicios_hero_texto', $oikos_page_id ),
		'link'           => oikos_link_or_cta( oikos_field( 'oikos_servicios_hero_boton', $oikos_page_id, array() ) ),
		'track_location' => 'servicios-hero',
	)
);

// Acompañamientos: carrusel de servicios. Las tarjetas salen de oikos_get_service_cards().
get_template_part(
	'template-parts/sections/services-carousel',
	null,
	array(
		'title'          => (string) oikos_field( 'oikos_servicios_acompanamientos_titulo', $oikos_page_id ),
		'text'           => (string) oikos_field( 'oikos_servicios_acompanamientos_texto', $oikos_page_id ),
		'track_location' => 'carrusel-servicios',
	)
);

// Aquí irán las demás secciones (por qué asistir a terapia…).

// Preguntas frecuentes (grupo ACF "Preguntas frecuentes" de la página) + bloque WhatsApp.
oikos_the_faq_section( 'faq-servicios', $oikos_page_id );

get_footer();
