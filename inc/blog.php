<?php
/**
 * Helpers del blog: autores (CPT equipo), categoría principal, color de
 * etiqueta y selección de entradas para los bloques de blog.
 *
 * Funcionan sin ACF ni Yoast (usan oikos_field() y comprueban las clases).
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Colores permitidos para la etiqueta de categoría.
 *
 * @return string[]
 */
function oikos_tag_colors() {
	return array( 'crema', 'arena', 'sage' );
}

/**
 * IDs de los miembros del equipo asignados como autores de una entrada.
 *
 * Solo los publicados y en el orden elegido en el campo.
 *
 * @param int $post_id ID de la entrada.
 * @return int[]
 */
function oikos_post_team_authors( $post_id ) {
	$ids = array();

	foreach ( (array) oikos_field( 'oikos_entrada_autores', $post_id, array() ) as $member ) {
		$member_id = $member instanceof WP_Post ? $member->ID : absint( $member );

		if ( $member_id && 'equipo' === get_post_type( $member_id ) && 'publish' === get_post_status( $member_id ) ) {
			$ids[] = $member_id;
		}
	}

	return array_values( array_unique( $ids ) );
}

/**
 * Autores visibles de una entrada.
 *
 * Los del campo ACF "Autores" (CPT equipo); si está vacío, el autor de WordPress.
 *
 * @param int $post_id ID de la entrada.
 * @return array[] Lista de array{id:int,name:string,type:string} (type: 'equipo' | 'user').
 */
function oikos_post_authors( $post_id ) {
	$authors = array();

	foreach ( oikos_post_team_authors( $post_id ) as $member_id ) {
		$authors[] = array(
			'id'   => $member_id,
			'name' => get_the_title( $member_id ),
			'type' => 'equipo',
		);
	}

	if ( ! $authors ) {
		$user_id = (int) get_post_field( 'post_author', $post_id );
		$name    = $user_id ? get_the_author_meta( 'display_name', $user_id ) : '';

		if ( '' !== $name ) {
			$authors[] = array(
				'id'   => $user_id,
				'name' => $name,
				'type' => 'user',
			);
		}
	}

	return $authors;
}

/**
 * Categoría principal de una entrada.
 *
 * La principal de Yoast si existe; si no, la primera. Ignora la categoría
 * por defecto ("Sin categoría").
 *
 * @param int $post_id ID de la entrada.
 * @return WP_Term|null
 */
function oikos_post_category( $post_id ) {
	$default = (int) get_option( 'default_category' );

	if ( class_exists( 'WPSEO_Primary_Term' ) ) {
		$primary = new WPSEO_Primary_Term( 'category', $post_id );
		$term    = get_term( (int) $primary->get_primary_term() );

		if ( $term instanceof WP_Term && $term->term_id !== $default && has_term( $term->term_id, 'category', $post_id ) ) {
			return $term;
		}
	}

	$terms = get_the_terms( $post_id, 'category' );

	if ( ! is_array( $terms ) ) {
		return null;
	}

	foreach ( $terms as $term ) {
		if ( $term->term_id !== $default ) {
			return $term;
		}
	}

	return null;
}

/**
 * Color de la etiqueta de una categoría.
 *
 * @param int $term_id ID de la categoría.
 * @return string Uno de oikos_tag_colors(); 'crema' por defecto.
 */
function oikos_category_color( $term_id ) {
	$color = (string) oikos_field( 'oikos_categoria_color', 'term_' . absint( $term_id ), 'crema' );

	return in_array( $color, oikos_tag_colors(), true ) ? $color : 'crema';
}

/**
 * Entradas para un bloque de blog.
 *
 * Orden de prioridad, sin repetir:
 * 1. posts:    IDs elegidos a mano (solo publicadas).
 * 2. category: entradas de esa categoría, las más recientes.
 * 3. Las más recientes del blog.
 *
 * @param array $args {
 *     @type int[]          $posts    IDs elegidos a mano.
 *     @type int|string     $category ID o slug de la categoría a priorizar.
 *     @type int            $count    Cuántas entradas (3).
 *     @type int[]          $exclude  IDs a excluir (ej. la entrada actual).
 * }
 * @return int[]
 */
function oikos_get_block_posts( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'posts'    => array(),
			'category' => 0,
			'count'    => 3,
			'exclude'  => array(),
		)
	);

	$count   = max( 1, (int) $args['count'] );
	$exclude = array_map( 'absint', (array) $args['exclude'] );
	$ids     = array();

	foreach ( (array) $args['posts'] as $post ) {
		$post_id = $post instanceof WP_Post ? $post->ID : absint( $post );

		if ( $post_id && 'post' === get_post_type( $post_id ) && 'publish' === get_post_status( $post_id ) && ! in_array( $post_id, $exclude, true ) ) {
			$ids[] = $post_id;
		}
	}

	$ids = array_slice( array_values( array_unique( $ids ) ), 0, $count );

	$fill = static function ( $ids, $extra_query ) use ( $count, $exclude ) {
		$missing = $count - count( $ids );

		if ( $missing < 1 ) {
			return $ids;
		}

		$found = get_posts(
			array_merge(
				array(
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'posts_per_page'      => $missing,
					'post__not_in'        => array_merge( $ids, $exclude ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
					'fields'              => 'ids',
				),
				$extra_query
			)
		);

		return array_merge( $ids, array_map( 'intval', $found ) );
	};

	$category = $args['category'];

	if ( $category ) {
		$term = is_numeric( $category ) ? get_term( (int) $category, 'category' ) : get_term_by( 'slug', (string) $category, 'category' );

		if ( $term instanceof WP_Term ) {
			$ids = $fill( $ids, array( 'cat' => $term->term_id ) );
		}
	}

	return $fill( $ids, array() );
}
