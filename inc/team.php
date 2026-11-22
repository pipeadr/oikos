<?php
/**
 * Equipo (CPT equipo): selección de miembros para las secciones de equipo.
 *
 * El CPT es la fuente única: nombre = título, foto = imagen destacada, bio
 * corta = oikos_equipo_bio_corta. Las páginas solo eligen y ordenan miembros.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Miembros del equipo publicados.
 *
 * @param array $ids IDs elegidos a mano (en ese orden). Vacío → todos,
 *                   por orden de menú y luego por fecha.
 * @return int[]
 */
function oikos_get_team_members( $ids = array() ) {
	$members = array();

	foreach ( (array) $ids as $member ) {
		$member_id = $member instanceof WP_Post ? $member->ID : absint( $member );

		if ( $member_id && 'equipo' === get_post_type( $member_id ) && 'publish' === get_post_status( $member_id ) ) {
			$members[] = $member_id;
		}
	}

	if ( $members ) {
		return array_values( array_unique( $members ) );
	}

	return array_map(
		'intval',
		get_posts(
			array(
				'post_type'      => 'equipo',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				),
				'no_found_rows'  => true,
				'fields'         => 'ids',
			)
		)
	);
}

/**
 * Miembros de la sección de equipo de Servicios general.
 *
 * Misma regla para la plantilla y el schema: título vacío → sin sección.
 *
 * @param int $page_id ID de la página.
 * @return int[]
 */
function oikos_servicios_team_members( $page_id ) {
	if ( '' === trim( (string) oikos_field( 'oikos_servicios_equipo_titulo', $page_id ) ) ) {
		return array();
	}

	return oikos_get_team_members( (array) oikos_field( 'oikos_servicios_equipo_miembros', $page_id, array() ) );
}

/**
 * Texto alternativo de la foto de un miembro.
 *
 * El de la biblioteca de medios; si está vacío, uno con el nombre.
 *
 * @param int $member_id ID del CPT equipo.
 * @param int $image_id  ID del adjunto.
 * @return string
 */
function oikos_team_photo_alt( $member_id, $image_id ) {
	$alt = trim( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );

	if ( '' !== $alt ) {
		return $alt;
	}

	/* translators: %s: nombre del miembro del equipo. */
	return sprintf( __( 'Foto de %s, psicólogo/a de Oikos', 'oikos' ), wp_strip_all_tags( get_the_title( $member_id ) ) );
}

/**
 * Miembros del equipo que muestra una página (para el schema).
 *
 * Cada plantilla con sección de equipo se suma aquí. Otras pueden usar el
 * filtro oikos_schema_page_team_members.
 *
 * @param int $page_id ID de la página.
 * @return int[]
 */
function oikos_page_team_members( $page_id ) {
	$ids = array();

	if ( 'templates/servicios.php' === get_page_template_slug( $page_id ) ) {
		$ids = oikos_servicios_team_members( $page_id );
	}

	/**
	 * Filtra los miembros del equipo que muestra una página.
	 *
	 * @param int[] $ids     IDs del CPT equipo.
	 * @param int   $page_id ID de la página.
	 */
	return array_values( array_unique( array_map( 'absint', (array) apply_filters( 'oikos_schema_page_team_members', $ids, $page_id ) ) ) );
}
