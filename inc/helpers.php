<?php
/**
 * Helpers del tema.
 *
 * Envoltorios seguros para que el tema no se rompa si ACF o Yoast
 * están desactivados, y utilidades de rutas de assets.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Devuelve el valor de un campo ACF o un valor por defecto si ACF no está activo.
 *
 * @param string     $name    Nombre del campo.
 * @param int|string $post_id ID del post (o 'option'). false = post actual.
 * @param mixed      $default Valor si el campo está vacío o ACF no existe.
 * @return mixed
 */
function oikos_field( $name, $post_id = false, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $name, $post_id );

	return ( null === $value || '' === $value || false === $value ) ? $default : $value;
}

/**
 * Devuelve un campo de la página de opciones "Oikos — Global".
 *
 * @param string $name    Nombre del campo.
 * @param mixed  $default Valor por defecto.
 * @return mixed
 */
function oikos_option( $name, $default = '' ) {
	return oikos_field( $name, 'option', $default );
}

/**
 * Imprime las migas de pan de Yoast si el plugin está activo.
 *
 * @param string $before HTML antes de las migas.
 * @param string $after  HTML después de las migas.
 * @return void
 */
function oikos_breadcrumbs( $before = '<nav class="oikos-breadcrumbs" aria-label="Breadcrumb">', $after = '</nav>' ) {
	if ( ! function_exists( 'yoast_breadcrumb' ) ) {
		return;
	}

	yoast_breadcrumb( $before, $after );
}

/**
 * URL de un asset del tema.
 *
 * @param string $path Ruta relativa dentro de /assets (ej. 'css/base.css').
 * @return string
 */
function oikos_asset_uri( $path ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

/**
 * Ruta absoluta en disco de un asset del tema.
 *
 * @param string $path Ruta relativa dentro de /assets.
 * @return string
 */
function oikos_asset_path( $path ) {
	return get_template_directory() . '/assets/' . ltrim( $path, '/' );
}

/**
 * Versión de un asset basada en su fecha de modificación (cache-busting).
 *
 * @param string $path Ruta relativa dentro de /assets.
 * @return string|null
 */
function oikos_asset_version( $path ) {
	$file = oikos_asset_path( $path );

	return file_exists( $file ) ? (string) filemtime( $file ) : null;
}

/**
 * Enlace tel: a partir del teléfono de "Oikos — Global".
 *
 * @param string $phone Teléfono tal como se muestra (ej. '+57 300 123 4567').
 * @return string URL tel: o '' si no hay dígitos.
 */
function oikos_phone_href( $phone ) {
	$phone  = trim( (string) $phone );
	$digits = preg_replace( '/\D+/', '', $phone );

	if ( '' === $digits ) {
		return '';
	}

	return 'tel:' . ( 0 === strpos( $phone, '+' ) ? '+' : '' ) . $digits;
}

/**
 * URL de WhatsApp (wa.me) con el número y mensaje de "Oikos — Global".
 *
 * @return string URL o '' si no hay número.
 */
function oikos_whatsapp_url() {
	$number = preg_replace( '/\D+/', '', (string) oikos_option( 'oikos_global_whatsapp' ) );

	if ( '' === $number ) {
		return '';
	}

	$url     = 'https://wa.me/' . $number;
	$message = trim( (string) oikos_option( 'oikos_global_whatsapp_mensaje' ) );

	if ( '' !== $message ) {
		$url .= '?text=' . rawurlencode( $message );
	}

	return $url;
}

/**
 * Nombre de la marca (copyright y alt de respaldo de los logos).
 *
 * @return string
 */
function oikos_brand_name() {
	return (string) oikos_option( 'oikos_global_marca_nombre', get_bloginfo( 'name' ) );
}

/**
 * ¿La plantilla actual empieza con un hero a sangre bajo el header?
 *
 * Las plantillas con hero lo activan con:
 * add_filter( 'oikos_has_hero', '__return_true' );
 *
 * @return bool
 */
function oikos_has_hero() {
	return (bool) apply_filters( 'oikos_has_hero', false );
}

/**
 * Markup <img> de un adjunto pensado para logos (SVG o PNG).
 *
 * No usa wp_get_attachment_image() porque en SVG imprime width/height 0 o 1
 * y el logo desaparece o se deforma. Aquí width/height solo se imprimen si
 * los metadatos son reales (PNG, evita CLS); en SVG el tamaño lo da el CSS.
 *
 * @param int   $attachment_id ID del adjunto.
 * @param array $attr          Atributos extra (class, loading, fetchpriority…).
 * @return string '' si el adjunto no existe.
 */
function oikos_logo_img( $attachment_id, $attr = array() ) {
	$attachment_id = absint( $attachment_id );
	$src           = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'full' ) : '';

	if ( ! $src ) {
		return '';
	}

	$alt = trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );

	$attr = array_merge(
		array(
			'src'      => $src,
			'alt'      => '' !== $alt ? $alt : oikos_brand_name(),
			'decoding' => 'async',
		),
		$attr
	);

	$meta = wp_get_attachment_metadata( $attachment_id );

	if ( is_array( $meta ) && ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && $meta['width'] > 1 && $meta['height'] > 1 ) {
		$attr['width']  = (int) $meta['width'];
		$attr['height'] = (int) $meta['height'];
	}

	$html = '<img';
	foreach ( $attr as $name => $value ) {
		if ( '' === $value || null === $value || false === $value ) {
			continue;
		}
		$html .= sprintf( ' %s="%s"', esc_attr( $name ), 'src' === $name ? esc_url( $value ) : esc_attr( $value ) );
	}

	return $html . '>';
}

/**
 * Markup <picture> de la foto de un hero (imagen LCP).
 *
 * - <img> real con srcset/sizes, width/height (sin CLS) y alt de la biblioteca.
 * - Sin loading="lazy" y con fetchpriority="high": es la imagen LCP.
 * - Si hay imagen móvil, va en un <source> para < 768 px; el navegador
 *   descarga solo una de las dos.
 *
 * @param int    $image_id        ID de la imagen desktop.
 * @param int    $image_mobile_id ID de la imagen móvil (0 = sin imagen móvil).
 * @param string $class           Clase del <picture>.
 * @param array  $args            Opcional: 'size' (tamaño registrado, por defecto 'full')
 *                                y 'sizes' (atributo sizes, por defecto '100vw').
 * @return string '' si la imagen desktop no existe.
 */
function oikos_hero_picture( $image_id, $image_mobile_id = 0, $class = '', $args = array() ) {
	$image_id        = absint( $image_id );
	$image_mobile_id = absint( $image_mobile_id );
	$args            = wp_parse_args(
		$args,
		array(
			'size'  => 'full',
			'sizes' => '100vw',
		)
	);

	$img = $image_id ? wp_get_attachment_image(
		$image_id,
		$args['size'],
		false,
		array(
			'sizes'         => $args['sizes'],
			'loading'       => false,
			'fetchpriority' => 'high',
			'decoding'      => 'async',
		)
	) : '';

	if ( '' === $img ) {
		return '';
	}

	$source = '';

	if ( $image_mobile_id ) {
		$srcset = wp_get_attachment_image_srcset( $image_mobile_id, 'full' );
		$srcset = $srcset ? $srcset : wp_get_attachment_image_url( $image_mobile_id, 'full' );

		if ( $srcset ) {
			$source = sprintf(
				'<source media="(max-width: 47.9375rem)" srcset="%s" sizes="100vw">',
				esc_attr( $srcset )
			);
		}
	}

	return sprintf(
		'<picture class="%1$s">%2$s%3$s</picture>',
		esc_attr( $class ),
		$source,
		$img
	);
}

/**
 * Título con saltos de línea manuales (campos textarea de título).
 *
 * Cada línea se escapa y los saltos se vuelven <br> con la clase dada, para
 * que el CSS de la sección los oculte donde no apliquen (por lo general en
 * móvil, donde el título se reparte solo). Un título de una línea sale igual
 * que con esc_html().
 *
 * @param string $title       Título (puede traer saltos de línea).
 * @param string $break_class Clase de los <br>.
 * @return string HTML escapado.
 */
function oikos_title_lines_html( $title, $break_class ) {
	$lines = array_filter( array_map( 'trim', preg_split( '/\R/', (string) $title ) ), 'strlen' );

	return implode(
		sprintf( '<br class="%s"> ', esc_attr( $break_class ) ),
		array_map( 'esc_html', $lines )
	);
}

/**
 * Título con una parte resaltada en negrita.
 *
 * El editor encierra entre asteriscos lo que va en negrita:
 * "Así funciona una sesión de *terapia de pareja* en Oikos". Todo se escapa;
 * cada par de asteriscos se vuelve <strong class="oikos-highlight">. Un
 * asterisco sin pareja se deja tal cual. Sin asteriscos, sale igual que
 * esc_html().
 *
 * @param string $text Texto plano con *resaltados*.
 * @return string HTML escapado.
 */
function oikos_highlight_html( $text ) {
	return preg_replace(
		'/\*([^*\r\n]*\S[^*\r\n]*)\*/u',
		'<strong class="oikos-highlight">$1</strong>',
		esc_html( (string) $text )
	);
}

/**
 * Textos de los badges de una página.
 *
 * Los de la página (repetidor `<prefijo>_badges_lista`, máx. 3) y, si está
 * vacío, los globales de "Oikos — Global" (`oikos_global_badges`).
 *
 * @param int    $page_id ID de la página (0 = solo globales).
 * @param string $field   Nombre del repetidor propio de la página.
 * @return string[] Hasta 3 textos.
 */
function oikos_get_badges( $page_id = 0, $field = 'oikos_servicio_badges_lista' ) {
	$collect = static function ( $rows ) {
		$texts = array();
		foreach ( (array) $rows as $row ) {
			$text = isset( $row['texto'] ) ? trim( (string) $row['texto'] ) : '';
			if ( '' !== $text ) {
				$texts[] = $text;
			}
		}
		return array_slice( $texts, 0, 3 );
	};

	$badges = $page_id ? $collect( oikos_field( $field, $page_id, array() ) ) : array();

	return $badges ? $badges : $collect( oikos_option( 'oikos_global_badges', array() ) );
}
