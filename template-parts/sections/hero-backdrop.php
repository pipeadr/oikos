<?php
/**
 * Sección: hero con foto de fondo y texto a la derecha (variante "backdrop").
 *
 * Foto a sangre (por defecto en blanco y negro) con el H1 arriba a la
 * izquierda y el párrafo + botón a la derecha, centrados en vertical. Pensada
 * para fotos con las personas a la izquierda y el lado derecho claro (páginas
 * de servicio). La plantilla activa oikos_has_hero: el header flota encima.
 *
 * < 1024: H1 → foto 4:3 a ancho completo → texto y botón.
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 *
 * Args:
 * - image_id        (int)    Foto de fondo (LCP: sin lazy, fetchpriority="high").
 * - focus_x/focus_y (int)    Punto focal 0–100 (object-position).
 * - grayscale       (bool)   Pasa la foto a blanco y negro (true por defecto).
 * - title           (string) Texto del H1 (la plantilla aplica el respaldo al título de la página).
 *                            Lo que va entre *asteriscos* sale en negrita.
 * - text            (string) Párrafo (admite <br>).
 * - link            (array)  url, title, target.
 * - track_location  (string) data-track-location del botón.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'image_id'       => 0,
		'focus_x'        => 50,
		'focus_y'        => 50,
		'grayscale'      => true,
		'title'          => '',
		'text'           => '',
		'link'           => array(),
		'track_location' => 'hero',
	)
);

$oikos_focus_x = min( 100, max( 0, (int) $oikos_args['focus_x'] ) );
$oikos_focus_y = min( 100, max( 0, (int) $oikos_args['focus_y'] ) );
$oikos_link    = is_array( $oikos_args['link'] ) ? $oikos_args['link'] : array();
$oikos_picture = oikos_hero_picture(
	$oikos_args['image_id'],
	0,
	'oikos-hero-backdrop__media',
	array(
		'size'  => 'full',
		'sizes' => '100vw', // A sangre en todos los anchos (en móvil, ancho completo).
	)
);

$oikos_classes = 'oikos-hero-backdrop';
if ( $oikos_picture ) {
	$oikos_classes .= ' oikos-hero-backdrop--has-media' . ( $oikos_args['grayscale'] ? ' oikos-hero-backdrop--grayscale' : '' );
}
?>
<section
	class="<?php echo esc_attr( $oikos_classes ); ?>"
	aria-labelledby="oikos-hero-title"
	style="<?php echo esc_attr( sprintf( '--hero-focus: %d%% %d%%;', $oikos_focus_x, $oikos_focus_y ) ); ?>"
>
	<div class="oikos-container oikos-hero-backdrop__inner">
		<h1 id="oikos-hero-title" class="oikos-hero-backdrop__title oikos-text-display-sm"><?php echo oikos_highlight_html( $oikos_args['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapa dentro del helper. ?></h1>

		<?php echo $oikos_picture; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapado en oikos_hero_picture() / wp_get_attachment_image(). ?>

		<div class="oikos-hero-backdrop__content">
			<?php if ( '' !== trim( (string) $oikos_args['text'] ) ) : ?>
				<p class="oikos-hero-backdrop__text oikos-text-body oikos-text-secondary"><?php echo wp_kses( $oikos_args['text'], array( 'br' => array() ) ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $oikos_link['url'] ) && ! empty( $oikos_link['title'] ) ) : ?>
				<a
					class="oikos-btn oikos-btn--primary oikos-btn--block-mobile oikos-hero-backdrop__cta"
					href="<?php echo esc_url( $oikos_link['url'] ); ?>"
					<?php echo ( isset( $oikos_link['target'] ) && '_blank' === $oikos_link['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
					data-track="agenda-cita"
					data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
				><?php echo esc_html( $oikos_link['title'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
