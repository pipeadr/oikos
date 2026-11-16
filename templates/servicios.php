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

// ¿Por qué asistir a terapia? Beneficios con foto que rota. Título vacío → sin sección.
$oikos_benefits = array();
foreach ( (array) oikos_field( 'oikos_servicios_porque_beneficios', $oikos_page_id, array() ) as $oikos_row ) {
	$oikos_benefits[] = array(
		'title'    => $oikos_row['titulo'] ?? '',
		'text'     => $oikos_row['descripcion'] ?? '',
		'image_id' => $oikos_row['imagen'] ?? 0,
		'focus_x'  => $oikos_row['foco_x'] ?? 50,
		'focus_y'  => $oikos_row['foco_y'] ?? 50,
	);
}

// Rotación: oikos_field() trata false como vacío; aquí "apagado" debe valer.
// null = la página nunca se guardó con el campo → activada (valor por defecto).
$oikos_rotation = function_exists( 'get_field' ) ? get_field( 'oikos_servicios_porque_rotacion', $oikos_page_id ) : null;

get_template_part(
	'template-parts/sections/benefits',
	null,
	array(
		'id'       => 'porque-terapia',
		'title'    => (string) oikos_field( 'oikos_servicios_porque_titulo', $oikos_page_id ),
		'items'    => $oikos_benefits,
		'autoplay' => null === $oikos_rotation ? true : (bool) $oikos_rotation,
		'interval' => (int) oikos_field( 'oikos_servicios_porque_intervalo', $oikos_page_id, 7 ),
	)
);

// Aquí irán las demás secciones (temas de trabajo, trivia…).

// Preguntas frecuentes (grupo ACF "Preguntas frecuentes" de la página) + bloque WhatsApp.
oikos_the_faq_section( 'faq-servicios', $oikos_page_id );

get_footer();
