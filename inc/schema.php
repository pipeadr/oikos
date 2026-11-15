<?php
/**
 * Datos estructurados: extensiones del grafo de Yoast.
 *
 * El tema nunca imprime JSON-LD suelto: modifica el grafo de Yoast con sus
 * filtros para que todo quede en un solo @graph conectado.
 *
 * Autores del blog (CPT equipo):
 * - Si la entrada tiene autores en el campo ACF "Autores", el Article apunta
 *   a un Person por cada miembro del equipo (@id estable por ID del CPT).
 * - El Person del usuario de WordPress deja de generarse en esas entradas
 *   (no es el autor real). La representación del sitio no se toca.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Miembros del equipo autores de la entrada del contexto de Yoast.
 *
 * @param object $context Meta_Tags_Context de Yoast.
 * @return int[]
 */
function oikos_schema_context_team_authors( $context ) {
	if ( ! is_object( $context ) || empty( $context->post ) || ! $context->post instanceof WP_Post ) {
		return array();
	}

	if ( 'post' !== $context->post->post_type ) {
		return array();
	}

	return oikos_post_team_authors( $context->post->ID );
}

/**
 * @id del Person de un miembro del equipo.
 *
 * @param int $member_id ID del CPT equipo.
 * @return string
 */
function oikos_schema_person_id( $member_id ) {
	return trailingslashit( home_url() ) . '#/schema/person/equipo-' . absint( $member_id );
}

/**
 * Sin el Person del usuario de WP en entradas con autores del equipo.
 *
 * Yoast no pasa el contexto a este filtro: se usa la consulta actual.
 *
 * @param bool $is_needed Valor de Yoast.
 * @return bool
 */
function oikos_schema_needs_author( $is_needed ) {
	if ( ! $is_needed || ! is_singular( 'post' ) ) {
		return $is_needed;
	}

	return oikos_post_team_authors( get_queried_object_id() ) ? false : $is_needed;
}
add_filter( 'wpseo_schema_needs_author', 'oikos_schema_needs_author' );

/**
 * Article: author = referencias a los Person del equipo.
 *
 * @param array  $data    Pieza Article de Yoast.
 * @param object $context Meta_Tags_Context.
 * @return array
 */
function oikos_schema_article_authors( $data, $context ) {
	$members = oikos_schema_context_team_authors( $context );

	if ( ! $members ) {
		return $data;
	}

	$authors = array();
	foreach ( $members as $member_id ) {
		$authors[] = array(
			'name' => get_the_title( $member_id ),
			'@id'  => oikos_schema_person_id( $member_id ),
		);
	}

	$data['author'] = 1 === count( $authors ) ? $authors[0] : $authors;

	return $data;
}
add_filter( 'wpseo_schema_article', 'oikos_schema_article_authors', 10, 2 );

/**
 * WebPage: Yoast también lo enlaza al Person del usuario de WP, que en estas
 * entradas ya no se genera. Se apunta a los mismos Person del equipo.
 *
 * @param array  $data    Pieza WebPage de Yoast.
 * @param object $context Meta_Tags_Context.
 * @return array
 */
function oikos_schema_webpage_authors( $data, $context ) {
	if ( ! isset( $data['author'] ) ) {
		return $data;
	}

	$members = oikos_schema_context_team_authors( $context );

	if ( ! $members ) {
		return $data;
	}

	$refs = array();
	foreach ( $members as $member_id ) {
		$refs[] = array( '@id' => oikos_schema_person_id( $member_id ) );
	}

	$data['author'] = 1 === count( $refs ) ? $refs[0] : $refs;

	return $data;
}
add_filter( 'wpseo_schema_webpage', 'oikos_schema_webpage_authors', 10, 2 );

/**
 * Agrega al grafo un Person por cada miembro del equipo autor de la entrada.
 *
 * @param array  $graph   Grafo de Yoast.
 * @param object $context Meta_Tags_Context.
 * @return array
 */
function oikos_schema_graph_team_authors( $graph, $context ) {
	$members = oikos_schema_context_team_authors( $context );

	if ( ! $members || ! is_array( $graph ) ) {
		return $graph;
	}

	// Solo si Yoast generó el Article (que es quien referencia a los Person).
	$has_article = false;
	foreach ( $graph as $piece ) {
		if ( isset( $piece['author'] ) && is_array( $piece['author'] ) ) {
			$has_article = true;
			break;
		}
	}

	if ( ! $has_article ) {
		return $graph;
	}

	$org_id = ( isset( $context->site_represents ) && 'company' === $context->site_represents && ! empty( $context->site_url ) )
		? $context->site_url . '#organization'
		: '';

	foreach ( $members as $member_id ) {
		$graph[] = oikos_schema_team_person( $member_id, $org_id );
	}

	return $graph;
}
add_filter( 'wpseo_schema_graph', 'oikos_schema_graph_team_authors', 10, 2 );

/**
 * Pieza Person de un miembro del equipo.
 *
 * @param int    $member_id ID del CPT equipo.
 * @param string $org_id    @id de la Organization de Yoast ('' = sin worksFor).
 * @return array
 */
function oikos_schema_team_person( $member_id, $org_id = '' ) {
	$person = array(
		'@type' => 'Person',
		'@id'   => oikos_schema_person_id( $member_id ),
		'name'  => wp_strip_all_tags( get_the_title( $member_id ) ),
	);

	$job = trim( (string) oikos_field( 'oikos_equipo_enfoque', $member_id ) );
	if ( '' !== $job ) {
		$person['jobTitle'] = $job;
	}

	$bio = trim( wp_strip_all_tags( (string) oikos_field( 'oikos_equipo_bio_corta', $member_id ) ) );
	if ( '' !== $bio ) {
		$person['description'] = $bio;
	}

	$image_id = (int) get_post_thumbnail_id( $member_id );
	$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';
	if ( $image ) {
		$person['image'] = array(
			'@type'      => 'ImageObject',
			'@id'        => oikos_schema_person_id( $member_id ) . '/image',
			'url'        => $image,
			'contentUrl' => $image,
			'caption'    => $person['name'],
		);
	}

	if ( oikos_equipo_is_public() ) {
		$person['url'] = get_permalink( $member_id );
	}

	if ( '' !== $org_id ) {
		$person['worksFor'] = array( '@id' => $org_id );
	}

	/**
	 * Filtra el Person de un miembro del equipo (ej. agregar sameAs).
	 *
	 * @param array $person    Pieza Person.
	 * @param int   $member_id ID del CPT equipo.
	 */
	return apply_filters( 'oikos_schema_team_person', $person, $member_id );
}
