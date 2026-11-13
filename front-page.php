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

// Acompañamiento: tarjetas que enlazan a la página de cada servicio.
$oikos_service_cards = array();
foreach ( (array) oikos_field( 'oikos_home_acompanamiento_tarjetas', $oikos_front_id, array() ) as $oikos_row ) {
	$oikos_page_id = isset( $oikos_row['pagina'] ) ? absint( $oikos_row['pagina'] ) : 0;

	if ( ! $oikos_page_id || 'publish' !== get_post_status( $oikos_page_id ) ) {
		continue;
	}

	$oikos_text            = isset( $oikos_row['texto'] ) ? trim( (string) $oikos_row['texto'] ) : '';
	$oikos_service_cards[] = array(
		'url'   => get_permalink( $oikos_page_id ),
		'text'  => '' !== $oikos_text ? $oikos_text : get_the_title( $oikos_page_id ),
		'color' => isset( $oikos_row['color'] ) ? (string) $oikos_row['color'] : 'arena',
	);
}

// Pasos.
$oikos_steps = array();
foreach ( (array) oikos_field( 'oikos_home_pasos_lista', $oikos_front_id, array() ) as $oikos_row ) {
	$oikos_steps[] = array(
		'title' => isset( $oikos_row['titulo'] ) ? (string) $oikos_row['titulo'] : '',
		'text'  => isset( $oikos_row['texto'] ) ? (string) $oikos_row['texto'] : '',
	);
}
?>
<div class="oikos-home-journey">
	<?php oikos_the_decor( 'decor-lineas', 'oikos-home-journey__decor' ); ?>

	<?php
	get_template_part(
		'template-parts/sections/service-cards',
		null,
		array(
			'title'          => (string) oikos_field( 'oikos_home_acompanamiento_titulo', $oikos_front_id ),
			'subtitle'       => (string) oikos_field( 'oikos_home_acompanamiento_subtitulo', $oikos_front_id ),
			'heading_level'  => 2,
			'cards'          => $oikos_service_cards,
			'link'           => (array) oikos_field( 'oikos_home_acompanamiento_boton', $oikos_front_id, array() ),
			'track_location' => 'home-acompanamiento',
		)
	);

	get_template_part(
		'template-parts/sections/steps',
		null,
		array(
			'layout'         => 'split',
			'background'     => 'none', // El fondo lo da .oikos-home-journey.
			'eyebrow'        => (string) oikos_field( 'oikos_home_pasos_eyebrow', $oikos_front_id ),
			'title'          => (string) oikos_field( 'oikos_home_pasos_titulo', $oikos_front_id ),
			'heading_level'  => 2,
			'steps'          => $oikos_steps,
			'link'           => oikos_link_or_cta( oikos_field( 'oikos_home_pasos_boton', $oikos_front_id, array() ) ),
			'button_variant' => 'primary',
			'track'          => 'agenda-cita',
			'track_location' => 'home-pasos',
		)
	);
	?>
</div>
<?php
get_footer();
