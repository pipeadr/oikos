<?php
/**
 * Sección: hero con foto a pantalla completa y texto (variante "cover").
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 *
 * Args:
 * - image_id        (int)      Foto de fondo (LCP).
 * - image_mobile_id (int)      Foto para < 768 px (opcional).
 * - focus_x/focus_y (int)      Punto focal 0–100 (object-position de la foto desktop).
 * - title           (string)   Texto del H1.
 * - text            (string)   Párrafo (admite <br>).
 * - link            (array)    url, title, target.
 * - cards           (string[]) Hasta 3 textos cortos.
 * - track_location  (string)   data-track-location del botón.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'image_id'        => 0,
		'image_mobile_id' => 0,
		'focus_x'         => 50,
		'focus_y'         => 50,
		'title'           => '',
		'text'            => '',
		'link'            => array(),
		'cards'           => array(),
		'track_location'  => 'hero',
	)
);

$oikos_focus_x = min( 100, max( 0, (int) $oikos_args['focus_x'] ) );
$oikos_focus_y = min( 100, max( 0, (int) $oikos_args['focus_y'] ) );
$oikos_picture = oikos_hero_picture( $oikos_args['image_id'], $oikos_args['image_mobile_id'], 'oikos-hero__media' );
$oikos_link    = is_array( $oikos_args['link'] ) ? $oikos_args['link'] : array();
$oikos_cards   = array_slice( array_filter( array_map( 'strval', (array) $oikos_args['cards'] ), 'strlen' ), 0, 3 );

$oikos_classes = 'oikos-hero oikos-hero--cover';
if ( $oikos_picture ) {
	$oikos_classes .= ' oikos-hero--has-media' . ( absint( $oikos_args['image_mobile_id'] ) ? ' oikos-hero--has-mobile' : '' );
}
?>
<section
	class="<?php echo esc_attr( $oikos_classes ); ?>"
	aria-labelledby="oikos-hero-title"
	style="<?php echo esc_attr( sprintf( '--hero-focus: %d%% %d%%;', $oikos_focus_x, $oikos_focus_y ) ); ?>"
>
	<?php echo $oikos_picture; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapado en oikos_hero_picture() / wp_get_attachment_image(). ?>

	<div class="oikos-hero__inner oikos-container">
		<div class="oikos-hero__content">
			<h1 id="oikos-hero-title" class="oikos-hero__title oikos-text-display"><?php echo esc_html( $oikos_args['title'] ); ?></h1>

			<?php if ( '' !== trim( (string) $oikos_args['text'] ) ) : ?>
				<p class="oikos-hero__text oikos-text-body oikos-text-secondary"><?php echo wp_kses( $oikos_args['text'], array( 'br' => array() ) ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $oikos_link['url'] ) && ! empty( $oikos_link['title'] ) ) : ?>
				<a
					class="oikos-btn oikos-btn--primary oikos-btn--block-mobile oikos-hero__cta"
					href="<?php echo esc_url( $oikos_link['url'] ); ?>"
					<?php echo ( isset( $oikos_link['target'] ) && '_blank' === $oikos_link['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
					data-track="agenda-cita"
					data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
				><?php echo esc_html( $oikos_link['title'] ); ?></a>
			<?php endif; ?>
		</div>

		<?php if ( $oikos_cards ) : ?>
			<ul class="oikos-hero__cards" role="list">
				<?php foreach ( $oikos_cards as $oikos_card ) : ?>
					<li class="oikos-card oikos-hero__card">
						<p class="oikos-text-card"><?php echo esc_html( $oikos_card ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
