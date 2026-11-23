<?php
/**
 * Servicios: fuente única de las tarjetas del carrusel de servicios.
 *
 * Los datos viven en dos lugares:
 * - Cada página de terapia (hija de "Servicios general"): grupo ACF
 *   "Tarjeta de servicio" (imagen, punto focal, descripción).
 * - La página "Servicios general", pestaña "Acompañamientos": orden de las
 *   tarjetas y texto del botón.
 * Todos los carruseles (Servicios, Nosotros, Contacto) leen de aquí.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * ID de la página con la plantilla "Servicios general".
 *
 * @return int 0 si no existe.
 */
function oikos_services_page_id() {
	static $id = null;

	if ( null !== $id ) {
		return $id;
	}

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Una consulta por petición (caché estática).
			'meta_value'     => 'templates/servicios.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'orderby'        => 'menu_order ID',
			'order'          => 'ASC',
		)
	);

	$id = $pages ? (int) $pages[0] : 0;

	return $id;
}

/**
 * ¿La página es una página de servicio (terapia)?
 *
 * Hoy: hija de la página "Servicios general". Cuando exista la plantilla
 * "Servicio" (templates/servicio.php), también las páginas que la usen.
 *
 * @param int $post_id   ID de la página.
 * @param int $parent_id Padre a evaluar (opcional; ACF lo envía al cambiarlo en el editor).
 * @return bool
 */
function oikos_is_service_page( $post_id, $parent_id = null ) {
	$post_id = absint( $post_id );

	if ( $post_id && 'templates/servicio.php' === get_page_template_slug( $post_id ) ) {
		return true;
	}

	$parent_id = null === $parent_id ? ( $post_id ? wp_get_post_parent_id( $post_id ) : 0 ) : absint( $parent_id );

	return $parent_id && 'templates/servicios.php' === get_page_template_slug( $parent_id );
}

/**
 * Tarjetas del carrusel de servicios, en orden.
 *
 * Orden: relación "Servicios" de la pestaña Acompañamientos; si está vacía,
 * las hijas publicadas de "Servicios general" por orden de página y título.
 *
 * @return array[] Lista de array{id:int,title:string,url:string,slug:string,image_id:int,focus_x:int,focus_y:int,text:string}.
 */
function oikos_get_service_cards() {
	$services_id = oikos_services_page_id();

	if ( ! $services_id ) {
		return array();
	}

	$ids = array_map( 'absint', (array) oikos_field( 'oikos_servicios_acompanamientos_servicios', $services_id, array() ) );

	if ( ! $ids ) {
		$ids = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_parent'    => $services_id,
				'posts_per_page' => 20,
				'fields'         => 'ids',
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
			)
		);
	}

	$cards = array();

	foreach ( $ids as $id ) {
		if ( ! $id || 'publish' !== get_post_status( $id ) ) {
			continue;
		}

		$image_id = (int) oikos_field( 'oikos_servicio_tarjeta_imagen', $id, 0 );

		$cards[] = array(
			'id'       => (int) $id,
			'title'    => get_the_title( $id ),
			'url'      => get_permalink( $id ),
			'slug'     => get_post_field( 'post_name', $id ),
			'image_id' => $image_id ? $image_id : (int) get_post_thumbnail_id( $id ),
			'focus_x'  => (int) oikos_field( 'oikos_servicio_tarjeta_foco_x', $id, 50 ),
			'focus_y'  => (int) oikos_field( 'oikos_servicio_tarjeta_foco_y', $id, 50 ),
			'text'     => trim( (string) oikos_field( 'oikos_servicio_tarjeta_descripcion', $id ) ),
		);
	}

	/**
	 * Filtra las tarjetas del carrusel de servicios.
	 *
	 * @param array[] $cards Tarjetas.
	 */
	return (array) apply_filters( 'oikos_service_cards', $cards );
}

/**
 * Texto del botón de las tarjetas de servicio ("Pide tu cita").
 *
 * @return string
 */
function oikos_service_card_button_text() {
	return (string) oikos_field( 'oikos_servicios_acompanamientos_boton_texto', oikos_services_page_id(), oikos_cta()['text'] );
}

/**
 * Categoría del blog que prioriza el bloque de entradas de una página de servicio.
 *
 * El campo `oikos_servicio_blog_categoria` de la página o, si está vacío, la
 * categoría cuyo slug coincide con el de la página (terapia-de-pareja →
 * "Terapia de pareja").
 *
 * @param int $page_id ID de la página de servicio.
 * @return int ID de la categoría (0 = ninguna: solo entradas recientes).
 */
function oikos_service_blog_category( $page_id ) {
	$term_id = absint( oikos_field( 'oikos_servicio_blog_categoria', $page_id, 0 ) );

	if ( $term_id && get_term( $term_id, 'category' ) instanceof WP_Term ) {
		return $term_id;
	}

	$term = get_term_by( 'slug', (string) get_post_field( 'post_name', $page_id ), 'category' );

	return $term instanceof WP_Term ? (int) $term->term_id : 0;
}

/**
 * Registra la ubicación ACF "Página de servicio" (no depende de IDs, así
 * funciona igual en cada entorno).
 *
 * @return void
 */
function oikos_register_service_page_location() {
	if ( ! function_exists( 'acf_register_location_type' ) || ! class_exists( 'ACF_Location' ) ) {
		return;
	}

	require_once get_template_directory() . '/inc/class-oikos-acf-location-service-page.php';
	acf_register_location_type( 'Oikos_ACF_Location_Service_Page' );
}
add_action( 'acf/init', 'oikos_register_service_page_location' );

/**
 * El campo de relación "Servicios" solo ofrece las hijas de la página editada.
 *
 * @param array      $args    Argumentos de WP_Query.
 * @param array      $field   Campo.
 * @param int|string $post_id Página editada.
 * @return array
 */
function oikos_acf_services_relationship_query( $args, $field, $post_id ) {
	$args['post_parent'] = absint( $post_id );
	$args['post_status'] = 'publish';

	return $args;
}
add_filter( 'acf/fields/relationship/query/name=oikos_servicios_acompanamientos_servicios', 'oikos_acf_services_relationship_query', 10, 3 );
