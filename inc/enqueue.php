<?php
/**
 * Encolado de CSS y JS.
 *
 * - base.css y main.js en todo el sitio.
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
	foreach ( array( 'button', 'card' ) as $oikos_component ) {
		wp_enqueue_style(
			'oikos-' . $oikos_component,
			oikos_asset_uri( 'css/' . $oikos_component . '.css' ),
			array( 'oikos-base' ),
			oikos_asset_version( 'css/' . $oikos_component . '.css' )
		);
	}

	wp_enqueue_script(
		'oikos-main',
		oikos_asset_uri( 'js/main.js' ),
		array(),
		oikos_asset_version( 'js/main.js' ),
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
		'oikos-style-guide' => array(
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
		'fonts/figtree-400-600.woff2',
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
