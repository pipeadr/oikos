<?php
/**
 * Encolado de CSS y JS.
 *
 * - base.css y los componentes globales en todo el sitio.
 * - Assets por plantilla: solo donde se necesitan (ver oikos_template_assets()).
 * - JS siempre con defer.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Encola los assets globales del tema.
 *
 * @return void
 */
function oikos_enqueue_assets() {
	wp_enqueue_style(
		'oikos-base',
		oikos_asset_uri( 'css/base.css' ),
		array(),
		oikos_asset_version( 'css/base.css' )
	);

	// Componentes globales (pequeños y presentes en casi todas las páginas).
	// forms: el formulario del newsletter está en el footer de todas las páginas.
	foreach ( array( 'button', 'card', 'header', 'footer', 'forms' ) as $oikos_component ) {
		wp_enqueue_style(
			'oikos-' . $oikos_component,
			oikos_asset_uri( 'css/' . $oikos_component . '.css' ),
			array( 'oikos-base' ),
			oikos_asset_version( 'css/' . $oikos_component . '.css' )
		);
	}

	// main.js (JS global) no se encola mientras esté vacío: ahorra una petición.
	// Si vuelve a tener código, restaurar aquí su wp_enqueue_script con defer.

	// Header: submenú, menú móvil y sticky (presente en todas las páginas).
	wp_enqueue_script(
		'oikos-header',
		oikos_asset_uri( 'js/header.js' ),
		array(),
		oikos_asset_version( 'js/header.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	// Eventos al dataLayer: solo si GTM está configurado y CF7 está activo.
	if ( '' !== oikos_gtm_id() && defined( 'WPCF7_VERSION' ) ) {
		wp_enqueue_script(
			'oikos-analytics',
			oikos_asset_uri( 'js/analytics.js' ),
			array(),
			oikos_asset_version( 'js/analytics.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	oikos_enqueue_template_assets();
}
add_action( 'wp_enqueue_scripts', 'oikos_enqueue_assets' );

/**
 * Mapa de assets por plantilla.
 *
 * Cada entrada: 'handle' => array(
 *     'condition' => callable que devuelve bool,
 *     'css'       => ruta relativa en /assets (opcional),
 *     'js'        => ruta relativa en /assets (opcional),
 * )
 *
 * Ejemplo:
 * 'oikos-trivia' => array(
 *     'condition' => static fn() => is_page_template( 'templates/servicios.php' ),
 *     'css'       => 'css/trivia.css',
 *     'js'        => 'js/trivia.js',
 * ),
 *
 * @return array
 */
function oikos_template_assets() {
	return array(
		'oikos-hero'          => array(
			'condition' => static fn() => is_front_page() && is_page(),
			'css'       => 'css/hero.css',
		),
		// Hero foto + texto: se agregan aquí otras páginas internas que lo usen.
		'oikos-hero-split'    => array(
			'condition' => static fn() => is_page_template( 'templates/servicios.php' ),
			'css'       => 'css/hero-split.css',
		),
		// Foto + texto: se agregan aquí Nosotros, Servicios y El método al construirlas.
		'oikos-media-text'    => array(
			'condition' => static fn() => is_front_page() && is_page(),
			'css'       => 'css/media-text.css',
		),
		// Pasos: se agregan aquí Servicios y la plantilla Servicio al construirlas.
		'oikos-steps'         => array(
			'condition' => static fn() => is_front_page() && is_page(),
			'css'       => 'css/steps.css',
		),
		'oikos-service-cards' => array(
			'condition' => static fn() => is_front_page() && is_page(),
			'css'       => 'css/service-cards.css',
		),
		'oikos-about'         => array(
			'condition' => static fn() => is_front_page() && is_page(),
			'css'       => 'css/about.css',
		),
		// Carrusel de servicios: se agregan aquí Nosotros y Contacto al construirlos.
		'oikos-services-carousel' => array(
			'condition' => static fn() => is_page_template( 'templates/servicios.php' ),
			'css'       => 'css/services-carousel.css',
		),
		// "¿Por qué asistir a terapia?" (beneficios con foto que rota).
		'oikos-benefits'      => array(
			'condition' => static fn() => is_page_template( 'templates/servicios.php' ),
			'css'       => 'css/benefits.css',
			'js'        => 'js/benefits.js',
		),
		// Slider: se agregan aquí Nosotros, Contacto y testimonios al construirlos.
		'oikos-slider'        => array(
			'condition' => static fn() => ( is_front_page() && is_page() ) || is_page_template( 'templates/servicios.php' ),
			'css'       => 'css/slider.css',
			'js'        => 'js/slider.js',
		),
		// Blog: se agregan aquí el archivo (is_home), la entrada (is_singular post)
		// y la plantilla Servicio al construirlos.
		'oikos-tag'           => array(
			'condition' => static fn() => ( is_front_page() && is_page() ) || is_page_template( 'templates/style-guide.php' ),
			'css'       => 'css/tag.css',
		),
		'oikos-post-card'     => array(
			'condition' => static fn() => is_front_page() && is_page(),
			'css'       => 'css/post-card.css',
		),
		'oikos-posts-block'   => array(
			'condition' => static fn() => is_front_page() && is_page(),
			'css'       => 'css/posts-block.css',
		),
		// Preguntas frecuentes: en cualquier vista cuya página tenga preguntas.
		'oikos-faq'           => array(
			'condition' => 'oikos_page_has_faq',
			'css'       => 'css/faq.css',
			'js'        => 'js/faq.js',
		),
		// Bloque WhatsApp: se agregan aquí otras vistas que lo usen sin FAQ.
		'oikos-whatsapp-cta'  => array(
			'condition' => 'oikos_page_has_faq',
			'css'       => 'css/whatsapp-cta.css',
		),
		'oikos-home'          => array(
			'condition' => static fn() => is_front_page() && is_page(),
			'css'       => 'css/home.css',
		),
		'oikos-style-guide'   => array(
			'condition' => static fn() => is_page_template( 'templates/style-guide.php' ),
			'css'       => 'css/style-guide.css',
			'js'        => 'js/style-guide.js',
		),
	);
}

/**
 * Fuentes que se precargan (las del primer pantallazo).
 *
 * @return string[] Rutas relativas en /assets.
 */
function oikos_preload_fonts() {
	return array(
		'fonts/bricolage-grotesque-500-opsz.woff2',
		'fonts/figtree-400-700.woff2',
	);
}

/**
 * Imprime los <link rel="preload"> de las fuentes.
 *
 * Usa la misma URL que resuelve base.css (sin ?ver) para que el navegador
 * reutilice la descarga.
 *
 * @return void
 */
function oikos_print_font_preloads() {
	foreach ( oikos_preload_fonts() as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( oikos_asset_uri( $font ) )
		);
	}
}
add_action( 'wp_head', 'oikos_print_font_preloads', 2 );

/**
 * Encola los assets de la plantilla actual según el mapa.
 *
 * @return void
 */
function oikos_enqueue_template_assets() {
	foreach ( oikos_template_assets() as $handle => $asset ) {
		if ( empty( $asset['condition'] ) || ! is_callable( $asset['condition'] ) || ! call_user_func( $asset['condition'] ) ) {
			continue;
		}

		if ( ! empty( $asset['css'] ) ) {
			wp_enqueue_style(
				$handle,
				oikos_asset_uri( $asset['css'] ),
				array( 'oikos-base' ),
				oikos_asset_version( $asset['css'] )
			);
		}

		if ( ! empty( $asset['js'] ) ) {
			wp_enqueue_script(
				$handle,
				oikos_asset_uri( $asset['js'] ),
				array(),
				oikos_asset_version( $asset['js'] ),
				array(
					'strategy'  => 'defer',
					'in_footer' => true,
				)
			);
		}
	}
}

/**
 * Quita los estilos de bloques donde no se renderiza contenido del editor.
 *
 * Las páginas y entradas los conservan porque su contenido puede usar bloques.
 *
 * @return void
 */
function oikos_dequeue_block_styles() {
	if ( is_singular() ) {
		return;
	}

	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'oikos_dequeue_block_styles', 100 );
