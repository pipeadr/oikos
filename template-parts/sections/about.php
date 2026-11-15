<?php
/**
 * Sección: "Nosotros" del Inicio — foto de fondo, título, tarjetas
 * translúcidas, botón y slider de fotos.
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 * Requiere los assets 'oikos-about' y 'oikos-slider'.
 *
 * Args:
 * - image_id       (int)    Foto de fondo (decorativa, lazy: no es LCP). Sin ella, fondo verde.
 * - title          (string) Título (obligatorio: sin título no se imprime nada).
 * - heading_level  (int)    2–4. Las tarjetas van un nivel más abajo.
 * - cards          (array)  Hasta 4 array{title:string,text:string}.
 * - link           (array)  url, title, target del botón. Opcional.
 * - gallery        (int[])  IDs de las fotos del slider. Con una sola no hay indicadores.
 * - track          (string) data-track del botón.
 * - track_location (string) data-track-location del botón.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'image_id'       => 0,
		'title'          => '',
		'heading_level'  => 2,
		'cards'          => array(),
		'link'           => array(),
		'gallery'        => array(),
		'track'          => '',
		'track_location' => '',
	)
);

$oikos_title = trim( (string) $oikos_args['title'] );

if ( '' === $oikos_title ) {
	return;
}

$oikos_level    = min( 4, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_link     = is_array( $oikos_args['link'] ) ? $oikos_args['link'] : array();
$oikos_title_id = wp_unique_id( 'oikos-about-' );

$oikos_cards = array();
foreach ( array_slice( (array) $oikos_args['cards'], 0, 4 ) as $oikos_card ) {
	$oikos_card_title = isset( $oikos_card['title'] ) ? trim( (string) $oikos_card['title'] ) : '';
	if ( '' !== $oikos_card_title ) {
		$oikos_cards[] = array(
			'title' => $oikos_card_title,
			'text'  => isset( $oikos_card['text'] ) ? trim( (string) $oikos_card['text'] ) : '',
		);
	}
}

// Fondo decorativo: alt vacío a propósito.
$oikos_bg = absint( $oikos_args['image_id'] ) ? wp_get_attachment_image(
	absint( $oikos_args['image_id'] ),
	'full',
	false,
	array(
		'class'    => 'oikos-about__bg',
		'alt'      => '',
		'loading'  => 'lazy',
		'decoding' => 'async',
		'sizes'    => '100vw',
	)
) : '';

// Fotos del slider: la primera sin lazy (es la que se ve), el resto lazy.
$oikos_slides = array();
foreach ( array_map( 'absint', (array) $oikos_args['gallery'] ) as $oikos_image_id ) {
	if ( ! $oikos_image_id || ! wp_attachment_is_image( $oikos_image_id ) ) {
		continue;
	}

	$oikos_slides[] = wp_get_attachment_image(
		$oikos_image_id,
		'full',
		false,
		array(
			'loading'  => $oikos_slides ? 'lazy' : 'eager',
			'decoding' => 'async',
			// Columna derecha: ancho completo del contenedor < 1024; 443 px en desktop (Figma).
			'sizes'    => '(max-width: 63.9375rem) calc(100vw - 2.5rem), 27.6875rem',
		)
	);
}

$oikos_classes = 'oikos-section oikos-about'
	. ( $oikos_bg ? ' oikos-about--has-bg' : '' )
	. ( $oikos_slides ? '' : ' oikos-about--no-slider' );
?>
<section class="<?php echo esc_attr( $oikos_classes ); ?>" aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>">
	<?php echo $oikos_bg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapa. ?>

	<div class="oikos-container oikos-about__inner">
		<div class="oikos-about__content">
			<?php
			printf(
				'<h%1$d id="%2$s" class="oikos-about__title oikos-text-heading oikos-text-inverse">%3$s</h%1$d>',
				(int) $oikos_level,
				esc_attr( $oikos_title_id ),
				esc_html( $oikos_title )
			);
			?>

			<?php if ( $oikos_cards ) : ?>
				<ul class="oikos-about__cards" role="list">
					<?php foreach ( $oikos_cards as $oikos_card ) : ?>
						<li class="oikos-about__card">
							<?php
							printf(
								'<h%1$d class="oikos-about__card-title oikos-text-card">%2$s</h%1$d>',
								(int) $oikos_level + 1,
								esc_html( $oikos_card['title'] )
							);
							?>
							<?php if ( '' !== $oikos_card['text'] ) : ?>
								<p class="oikos-about__card-text oikos-text-body"><?php echo esc_html( $oikos_card['text'] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $oikos_link['url'] ) && ! empty( $oikos_link['title'] ) ) : ?>
				<a
					class="oikos-btn oikos-btn--outline-light oikos-btn--block-mobile oikos-about__cta"
					href="<?php echo esc_url( $oikos_link['url'] ); ?>"
					<?php echo ( isset( $oikos_link['target'] ) && '_blank' === $oikos_link['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
					<?php if ( '' !== $oikos_args['track'] ) : ?>
						data-track="<?php echo esc_attr( $oikos_args['track'] ); ?>"
						data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
					<?php endif; ?>
				><?php echo esc_html( $oikos_link['title'] ); ?></a>
			<?php endif; ?>
		</div>

		<?php if ( $oikos_slides ) : ?>
			<div class="oikos-about__media">
				<?php
				get_template_part(
					'template-parts/components/slider',
					null,
					array(
						'slides'     => $oikos_slides,
						'label'      => __( 'Fotos de Oikos', 'oikos' ),
						'item_label' => __( 'foto', 'oikos' ),
						'class'      => 'oikos-slider--portrait oikos-about__slider',
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
