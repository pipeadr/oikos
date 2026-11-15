<?php
/**
 * Componente: "¿Tienes dudas? ¡Estamos a un mensaje!" + ícono de WhatsApp.
 *
 * Reutilizable por sí solo. El número y el mensaje predeterminado salen de
 * "Oikos — Global" → Contacto (oikos_whatsapp_url()). Sin número no imprime nada.
 *
 * Frase + ícono forman un solo enlace (Figma: frase en verde, ícono verde
 * WhatsApp sin fondo). La frase identifica el enlace, así que el ícono es de
 * apoyo y su verde claro (1.94:1) no tiene requisito de contraste (WCAG
 * 1.4.11). Sin aria-label: el nombre accesible incluye el texto visible
 * (WCAG 2.5.3) + "(WhatsApp, se abre en una pestaña nueva)".
 *
 * Args:
 * - text           (string) Frase. Vacío → oikos_global_whatsapp_cta_texto.
 * - track_location (string) data-track-location del enlace.
 * - class          (string) Clases extra del contenedor.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'text'           => '',
		'track_location' => '',
		'class'          => '',
	)
);

$oikos_url = oikos_whatsapp_url();

if ( '' === $oikos_url ) {
	return;
}

$oikos_text = trim( (string) $oikos_args['text'] );
if ( '' === $oikos_text ) {
	$oikos_text = trim( (string) oikos_option( 'oikos_global_whatsapp_cta_texto', __( '¿Tienes dudas? ¡Estamos a un mensaje!', 'oikos' ) ) );
}
?>
<div class="<?php echo esc_attr( trim( 'oikos-whatsapp-cta ' . $oikos_args['class'] ) ); ?>">
	<a
		class="oikos-whatsapp-cta__link"
		href="<?php echo esc_url( $oikos_url ); ?>"
		target="_blank"
		rel="noopener"
		data-track="whatsapp"
		<?php if ( '' !== $oikos_args['track_location'] ) : ?>
			data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
		<?php endif; ?>
	>
		<span class="oikos-whatsapp-cta__text oikos-text-button"><?php echo esc_html( $oikos_text ); ?></span>
		<span class="screen-reader-text"><?php esc_html_e( '(WhatsApp, se abre en una pestaña nueva)', 'oikos' ); ?></span>
		<?php oikos_the_icon( 'whatsapp', 'oikos-whatsapp-cta__icon' ); ?>
	</a>
</div>
