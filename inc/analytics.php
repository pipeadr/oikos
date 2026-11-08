<?php
/**
 * Analítica: Google Tag Manager y eventos que el tema empuja al dataLayer.
 *
 * Los eventos se configuran en GTM. El tema solo imprime el contenedor
 * (si hay ID en "Oikos — Global") y empuja lo que GTM no detecta solo:
 * el envío exitoso de Contact Form 7.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Patrón válido para un ID de contenedor de GTM.
 */
const OIKOS_GTM_ID_PATTERN = '/^GTM-[A-Z0-9]+$/';

/**
 * Devuelve el ID de GTM configurado, o '' si no hay uno válido.
 *
 * @return string
 */
function oikos_gtm_id() {
	static $id = null;

	if ( null === $id ) {
		$value = strtoupper( trim( (string) oikos_option( 'oikos_global_gtm_id' ) ) );
		$id    = preg_match( OIKOS_GTM_ID_PATTERN, $value ) ? $value : '';
	}

	return $id;
}

/**
 * Imprime el snippet de GTM lo más arriba posible del <head>.
 *
 * @return void
 */
function oikos_gtm_head() {
	$id = oikos_gtm_id();

	if ( '' === $id ) {
		return;
	}
	?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_js( $id ); ?>');</script>
<!-- End Google Tag Manager -->
	<?php
}
add_action( 'wp_head', 'oikos_gtm_head', 1 );

/**
 * Imprime el <noscript> de GTM justo después de abrir <body>.
 *
 * @return void
 */
function oikos_gtm_body() {
	$id = oikos_gtm_id();

	if ( '' === $id ) {
		return;
	}

	$src = add_query_arg( 'id', $id, 'https://www.googletagmanager.com/ns.html' );
	?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="<?php echo esc_url( $src ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<?php
}
add_action( 'wp_body_open', 'oikos_gtm_body', 1 );

/**
 * Agrega el nombre del formulario CF7 como atributo del <form>.
 *
 * El evento JS de CF7 trae el id del formulario pero no su título;
 * analytics.js lo lee de este atributo.
 *
 * @param array $atts Atributos adicionales del <form>.
 * @return array
 */
function oikos_cf7_form_name_attr( $atts ) {
	if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
		return $atts;
	}

	$form = WPCF7_ContactForm::get_current();

	if ( $form ) {
		$atts['data-oikos-form-name'] = $form->title();
	}

	return $atts;
}
add_filter( 'wpcf7_form_additional_atts', 'oikos_cf7_form_name_attr' );
