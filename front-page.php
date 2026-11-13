<?php
/**
 * Página de inicio.
 *
 * Secciones administrables desde el grupo ACF "Inicio" (acf-json/group_oikos_home.json).
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

// Si en Ajustes → Lectura la portada muestra las últimas entradas, usar el archivo del blog.
if ( ! is_page() ) {
	require get_home_template();
	return;
}

$oikos_front_id = get_queried_object_id();

// Botón del hero: el del campo o, si está vacío, el CTA global "Agenda tu cita".
$oikos_hero_link = oikos_link_or_cta( oikos_field( 'oikos_home_hero_boton', $oikos_front_id, array() ) );

$oikos_hero_cards = array();
foreach ( (array) oikos_field( 'oikos_home_hero_tarjetas', $oikos_front_id, array() ) as $oikos_row ) {
	if ( ! empty( $oikos_row['texto'] ) ) {
		$oikos_hero_cards[] = (string) $oikos_row['texto'];
	}
}

$oikos_hero = array(
	'image_id'        => (int) oikos_field( 'oikos_home_hero_imagen', $oikos_front_id, 0 ),
	'image_mobile_id' => (int) oikos_field( 'oikos_home_hero_imagen_movil', $oikos_front_id, 0 ),
	'focus_x'         => (int) oikos_field( 'oikos_home_hero_foco_x', $oikos_front_id, 50 ),
	'focus_y'         => (int) oikos_field( 'oikos_home_hero_foco_y', $oikos_front_id, 50 ),
	'title'           => (string) oikos_field( 'oikos_home_hero_titulo', $oikos_front_id, get_the_title( $oikos_front_id ) ),
	'text'            => (string) oikos_field( 'oikos_home_hero_texto', $oikos_front_id ),
	'link'            => $oikos_hero_link,
	'cards'           => $oikos_hero_cards,
	'track_location'  => 'hero',
);

// Con foto, el header flota sobre ella (body .oikos-has-hero).
if ( $oikos_hero['image_id'] && wp_attachment_is_image( $oikos_hero['image_id'] ) ) {
	add_filter( 'oikos_has_hero', '__return_true' );
}

get_header();

get_template_part( 'template-parts/sections/hero-cover', null, $oikos_hero );

// El método (foto + texto).
get_template_part(
	'template-parts/sections/media-text',
	null,
	array(
		'image_id'       => (int) oikos_field( 'oikos_home_metodo_imagen', $oikos_front_id, 0 ),
		'image_position' => (string) oikos_field( 'oikos_home_metodo_posicion_imagen', $oikos_front_id, 'left' ),
		'background'     => (string) oikos_field( 'oikos_home_metodo_fondo', $oikos_front_id, 'white' ),
		'eyebrow'        => (string) oikos_field( 'oikos_home_metodo_eyebrow', $oikos_front_id ),
		'title'          => (string) oikos_field( 'oikos_home_metodo_titulo', $oikos_front_id ),
		'heading_level'  => 2,
		'text'           => (string) oikos_field( 'oikos_home_metodo_texto', $oikos_front_id ),
		'link'           => (array) oikos_field( 'oikos_home_metodo_boton', $oikos_front_id, array() ),
		'button_variant' => (string) oikos_field( 'oikos_home_metodo_boton_variante', $oikos_front_id, 'secondary' ),
		'track'          => 'conoce-metodo',
		'track_location' => 'home-metodo',
	)
);

get_footer();
