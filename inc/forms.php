<?php
/**
 * Formularios: integración con Contact Form 7.
 *
 * - Sin <p>/<br> automáticos en el formulario: el markup lo define la
 *   plantilla de cada formulario con las clases del tema (.oikos-form__*).
 * - Sin el CSS de CF7: todos los estados (errores, respuesta, spinner) los
 *   estiliza assets/css/forms.css.
 * - El botón de envío recibe las clases de botón del tema y, en el formulario
 *   del newsletter, los atributos data-track para GTM.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Desactiva el autop de CF7 en el formulario (no en el cuerpo del correo).
 *
 * @param bool  $autop   Valor actual.
 * @param array $options Contexto ('for' => 'mail' en los correos).
 * @return bool
 */
function oikos_cf7_autop( $autop, $options = array() ) {
	if ( is_array( $options ) && isset( $options['for'] ) && 'mail' === $options['for'] ) {
		return $autop;
	}

	return false;
}
add_filter( 'wpcf7_autop_or_not', 'oikos_cf7_autop', 10, 2 );

add_filter( 'wpcf7_load_css', '__return_false' );

/**
 * ID del formulario CF7 asignado al newsletter en "Oikos — Global".
 *
 * @return int
 */
function oikos_newsletter_form_id() {
	return (int) oikos_option( 'oikos_global_newsletter_formulario', 0 );
}

/**
 * Agrega las clases de botón del tema al submit de CF7.
 *
 * Newsletter (fondo verde): variante clara + data-track="suscripcion".
 * Resto de formularios: variante primaria.
 *
 * @param string $html Markup de los campos del formulario.
 * @return string
 */
function oikos_cf7_submit_button( $html ) {
	if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
		return $html;
	}

	$form          = WPCF7_ContactForm::get_current();
	$newsletter_id = oikos_newsletter_form_id();
	$is_newsletter = $form && $newsletter_id > 0 && (int) $form->id() === $newsletter_id;

	$classes = 'oikos-btn ' . ( $is_newsletter ? 'oikos-btn--light' : 'oikos-btn--primary' );
	$attrs   = $is_newsletter ? ' data-track="suscripcion" data-track-location="footer-newsletter"' : '';

	return (string) preg_replace_callback(
		'/<input\b([^>]*?)\bclass="([^"]*\bwpcf7-submit\b[^"]*)"/',
		static function ( $m ) use ( $classes, $attrs ) {
			return '<input' . $m[1] . 'class="' . $m[2] . ' ' . esc_attr( $classes ) . '"' . $attrs;
		},
		$html
	);
}
add_filter( 'wpcf7_form_elements', 'oikos_cf7_submit_button' );
