<?php
/**
 * Template Name: Servicio
 *
 * Plantilla compartida de las páginas de terapia (pareja, individual,
 * familiar). Secciones administrables desde el grupo ACF "Servicio"
 * (acf-json/group_oikos_servicio.json), una pestaña por sección. La tarjeta
 * del carrusel de servicios sigue en el grupo "Tarjeta de servicio".
 *
 * Orden: hero → temas → así funciona + badges → psicólogos → blog → FAQ +
 * WhatsApp.
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
// Botón vacío → sin botón (aquí no se usa el CTA global de respaldo).
oikos_prepare_faq_section( 'faq-' . $oikos_slug, $oikos_page_id, array( 'cta_fallback' => false ) );

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

// Así funciona una sesión: pasos en 3 columnas. Título vacío → sin sección.
// Lo que va entre *asteriscos* en el título sale en negrita.
$oikos_session_steps = array();
foreach ( (array) oikos_field( 'oikos_servicio_sesion_pasos', $oikos_page_id, array() ) as $oikos_row ) {
	$oikos_session_steps[] = array(
		'title' => $oikos_row['titulo'] ?? '',
		'text'  => $oikos_row['texto'] ?? '',
	);
}

get_template_part(
	'template-parts/sections/steps',
	null,
	array(
		'layout'        => 'columns',
		'background'    => 'white',
		'title'         => (string) oikos_field( 'oikos_servicio_sesion_titulo', $oikos_page_id ),
		'heading_level' => 2,
		'steps'         => $oikos_session_steps,
	)
);

// Badges: los de la página o, si no tiene, los globales. Van pegados a los
// pasos sobre el mismo fondo blanco (misma sección visual).
$oikos_badges = oikos_get_badges( $oikos_page_id );
if ( $oikos_badges ) :
	?>
	<div class="oikos-service-badges">
		<div class="oikos-container">
			<?php get_template_part( 'template-parts/components/badges', null, array( 'items' => $oikos_badges ) ); ?>
		</div>
	</div>
	<?php
endif;

// Los psicólogos detrás de su proceso: foto vertical B/N a la derecha. Título vacío → sin sección.
get_template_part(
	'template-parts/sections/media-text',
	null,
	array(
		'image_id'       => (int) oikos_field( 'oikos_servicio_psicologos_imagen', $oikos_page_id, 0 ),
		'image_position' => 'right',
		'image_shape'    => 'portrait',
		'grayscale'      => true,
		'background'     => 'white',
		'title'          => (string) oikos_field( 'oikos_servicio_psicologos_titulo', $oikos_page_id ),
		'heading_level'  => 2,
		'text'           => wpautop( (string) oikos_field( 'oikos_servicio_psicologos_texto', $oikos_page_id ) ),
	)
);

// Blog: prioriza la categoría del servicio (campo o, si está vacío, la del mismo slug).
// Título vacío → sin sección; botón vacío → "Explora el blog".
get_template_part(
	'template-parts/sections/posts-block',
	null,
	array(
		'title'          => (string) oikos_field( 'oikos_servicio_blog_titulo', $oikos_page_id ),
		'heading_level'  => 2,
		'category'       => oikos_service_blog_category( $oikos_page_id ),
		'link'           => (array) oikos_field( 'oikos_servicio_blog_boton', $oikos_page_id, array() ),
		'track_location' => 'blog-' . $oikos_slug,
	)
);

// Preguntas frecuentes + bloque WhatsApp (preparadas antes de get_header()).
oikos_the_faq_section();

get_footer();
