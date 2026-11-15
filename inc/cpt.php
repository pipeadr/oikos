<?php
/**
 * Tipos de contenido propios.
 *
 * - equipo: psicólogos (Paula, Juan David). Se usan como autores del blog
 *   (campo ACF oikos_entrada_autores), en servicios y en Nosotros.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * ¿El equipo tiene páginas individuales públicas?
 *
 * Hoy no. Para activarlas: add_filter( 'oikos_equipo_public', '__return_true' );
 * y guardar Ajustes → Enlaces permanentes (regenera las reglas de URL).
 *
 * @return bool
 */
function oikos_equipo_is_public() {
	return (bool) apply_filters( 'oikos_equipo_public', false );
}

/**
 * Registra el CPT equipo.
 *
 * @return void
 */
function oikos_register_post_types() {
	$public = oikos_equipo_is_public();

	register_post_type(
		'equipo',
		array(
			'labels'              => array(
				'name'               => __( 'Equipo', 'oikos' ),
				'singular_name'      => __( 'Miembro del equipo', 'oikos' ),
				'menu_name'          => __( 'Equipo', 'oikos' ),
				'add_new'            => __( 'Añadir miembro', 'oikos' ),
				'add_new_item'       => __( 'Añadir miembro del equipo', 'oikos' ),
				'edit_item'          => __( 'Editar miembro del equipo', 'oikos' ),
				'new_item'           => __( 'Nuevo miembro del equipo', 'oikos' ),
				'view_item'          => __( 'Ver miembro del equipo', 'oikos' ),
				'search_items'       => __( 'Buscar en el equipo', 'oikos' ),
				'not_found'          => __( 'No hay miembros del equipo.', 'oikos' ),
				'not_found_in_trash' => __( 'No hay miembros del equipo en la papelera.', 'oikos' ),
				'all_items'          => __( 'Todo el equipo', 'oikos' ),
				'featured_image'     => __( 'Foto', 'oikos' ),
				'set_featured_image' => __( 'Elegir foto', 'oikos' ),
				'remove_featured_image' => __( 'Quitar foto', 'oikos' ),
				'use_featured_image' => __( 'Usar como foto', 'oikos' ),
			),
			'description'         => __( 'Psicólogos de Oikos. También son los autores del blog.', 'oikos' ),
			'public'              => true,
			'publicly_queryable'  => $public,
			'exclude_from_search' => ! $public,
			'show_in_nav_menus'   => $public,
			'has_archive'         => false,
			'show_in_rest'        => true,
			'menu_position'       => 21,
			'menu_icon'           => 'dashicons-groups',
			'supports'            => array( 'title', 'thumbnail', 'page-attributes', 'revisions' ),
			'rewrite'             => $public ? array(
				'slug'       => 'equipo',
				'with_front' => false,
			) : false,
		)
	);
}
add_action( 'init', 'oikos_register_post_types' );

/**
 * Placeholder del título del CPT equipo ("Nombre").
 *
 * @param string  $title Placeholder por defecto.
 * @param WP_Post $post  Post actual.
 * @return string
 */
function oikos_equipo_title_placeholder( $title, $post ) {
	return 'equipo' === $post->post_type ? __( 'Nombre completo', 'oikos' ) : $title;
}
add_filter( 'enter_title_here', 'oikos_equipo_title_placeholder', 10, 2 );

/**
 * Yoast: sin páginas públicas, el equipo no va al sitemap.
 *
 * @param bool   $excluded  Excluido o no.
 * @param string $post_type Tipo de contenido.
 * @return bool
 */
function oikos_equipo_sitemap_exclude( $excluded, $post_type ) {
	if ( 'equipo' === $post_type && ! oikos_equipo_is_public() ) {
		return true;
	}

	return $excluded;
}
add_filter( 'wpseo_sitemap_exclude_post_type', 'oikos_equipo_sitemap_exclude', 10, 2 );
