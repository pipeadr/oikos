<?php
/**
 * Componente: tarjeta de servicio (terapia) del carrusel de servicios.
 *
 * Foto que cubre la tarjeta (acercada al punto focal en reposo), título que
 * enlaza a la página del servicio, descripción y botón "Pide tu cita" al CTA
 * global. Al hover/foco (o activa en pantallas táctiles) la foto se aleja y
 * la descripción se despliega. Estilos en css/services-carousel.css.
 *
 * Sin enlaces anidados: el título y el botón son los únicos enlaces.
 * La descripción completa siempre está en el HTML; la vista previa de una
 * línea es un ::before con content: attr(data-preview) / "" (no duplica el
 * texto en el DOM ni lo leen los lectores de pantalla).
 *
 * No lee ACF: recibe los datos de oikos_get_service_cards().
 *
 * Args:
 * - card           (array)  id, title, url, slug, image_id, focus_x, focus_y, text.
 * - heading_level  (int)    3–5.
 * - button         (array)  text, url, target. Sin url o texto no hay botón.
 * - track_location (string) Base de data-track-location (se agrega -<slug> al botón).
 * - image_loading  (string) 'lazy' (por defecto) | 'eager'.
 * - sizes          (string) Atributo sizes de la foto.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'card'           => array(),
		'heading_level'  => 3,
		'button'         => array(),
		'track_location' => 'carrusel-servicios',
		'image_loading'  => 'lazy',
		// Foto acercada ×1.3 en reposo: se pide más resolución que el ancho de la tarjeta.
		// Tarjeta: ~85 % del ancho (móvil) · ~60 % (tablet) · 595 px (desktop).
		'sizes'          => '(max-width: 47.9375rem) 110vw, (max-width: 63.9375rem) 80vw, 48.375rem',
	)
);

$oikos_card = wp_parse_args(
	(array) $oikos_args['card'],
	array(
		'title'    => '',
		'url'      => '',
		'slug'     => '',
		'image_id' => 0,
		'focus_x'  => 50,
		'focus_y'  => 50,
		'text'     => '',
	)
);

$oikos_title = trim( (string) $oikos_card['title'] );

if ( '' === $oikos_title ) {
	return;
}

$oikos_level    = min( 5, max( 3, (int) $oikos_args['heading_level'] ) );
$oikos_text     = trim( (string) $oikos_card['text'] );
$oikos_button   = wp_parse_args( (array) $oikos_args['button'], array( 'text' => '', 'url' => '', 'target' => '' ) );
$oikos_location = (string) $oikos_args['track_location'];
$oikos_focus    = sprintf( '--card-focus: %d%% %d%%;', min( 100, max( 0, (int) $oikos_card['focus_x'] ) ), min( 100, max( 0, (int) $oikos_card['focus_y'] ) ) );

// Foto decorativa: el enlace del título ya nombra el destino.
$oikos_image = absint( $oikos_card['image_id'] ) ? wp_get_attachment_image(
	absint( $oikos_card['image_id'] ),
	'full',
	false,
	array(
		'class'    => 'oikos-therapy-card__img',
		'alt'      => '',
		'loading'  => 'eager' === $oikos_args['image_loading'] ? 'eager' : 'lazy',
		'decoding' => 'async',
		'sizes'    => $oikos_args['sizes'],
	)
) : '';

$oikos_classes = 'oikos-therapy-card' . ( $oikos_image ? '' : ' oikos-therapy-card--no-image' ) . ( '' !== $oikos_text ? ' oikos-therapy-card--has-text' : '' );
?>
<article class="<?php echo esc_attr( $oikos_classes ); ?>" style="<?php echo esc_attr( $oikos_focus ); ?>">
	<?php if ( $oikos_image ) : ?>
		<div class="oikos-therapy-card__media">
			<?php echo $oikos_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapa. ?>
		</div>
	<?php endif; ?>

	<div class="oikos-therapy-card__body">
		<?php
		printf(
			'<h%1$d class="oikos-therapy-card__title">%2$s</h%1$d>',
			(int) $oikos_level,
			'' !== $oikos_card['url']
				? sprintf(
					'<a class="oikos-therapy-card__link" href="%1$s" data-track="servicio-card" data-track-location="%2$s">%3$s</a>',
					esc_url( $oikos_card['url'] ),
					esc_attr( $oikos_location ),
					esc_html( $oikos_title )
				)
				: esc_html( $oikos_title )
		);
		?>

		<?php if ( '' !== $oikos_text ) : ?>
			<div class="oikos-therapy-card__desc" data-preview="<?php echo esc_attr( $oikos_text ); ?>">
				<p class="oikos-therapy-card__text oikos-text-body"><?php echo esc_html( $oikos_text ); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( '' !== $oikos_button['url'] && '' !== trim( (string) $oikos_button['text'] ) ) : ?>
		<a
			class="oikos-btn oikos-btn--light oikos-therapy-card__cta"
			href="<?php echo esc_url( $oikos_button['url'] ); ?>"
			<?php echo '_blank' === $oikos_button['target'] ? 'target="_blank" rel="noopener"' : ''; ?>
			data-track="agenda-cita"
			data-track-location="<?php echo esc_attr( $oikos_location . ( '' !== $oikos_card['slug'] ? '-' . $oikos_card['slug'] : '' ) ); ?>"
		><?php echo esc_html( $oikos_button['text'] ); ?><span class="screen-reader-text"><?php echo esc_html( ': ' . $oikos_title ); ?></span></a>
	<?php endif; ?>
</article>
