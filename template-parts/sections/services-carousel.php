<?php
/**
 * Sección: carrusel de servicios ("Encuentra el acompañamiento que necesitas").
 *
 * Texto a la izquierda (arriba en < 1024) y carrusel de tarjetas de terapia
 * que sangra hasta el borde derecho de la pantalla. Reutilizable en
 * Servicios, Nosotros y Contacto: las tarjetas salen siempre de
 * oikos_get_service_cards() (fuente única); cada plantilla pasa solo su
 * título y texto.
 *
 * Requiere los assets 'oikos-services-carousel' y 'oikos-slider'
 * (ver oikos_template_assets()).
 *
 * Args:
 * - title          (string)     Título (obligatorio).
 * - text           (string)     Párrafo (admite <br>). Opcional.
 * - heading_level  (int)        2–4. Las tarjetas van un nivel más abajo.
 * - cards          (array|null) null = oikos_get_service_cards().
 * - button_text    (string|null) null = oikos_service_card_button_text(). Lleva al CTA global.
 * - track_location (string)     data-track-location base de las tarjetas.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'title'          => '',
		'text'           => '',
		'heading_level'  => 2,
		'cards'          => null,
		'button_text'    => null,
		'track_location' => 'carrusel-servicios',
	)
);

$oikos_title = trim( (string) $oikos_args['title'] );
$oikos_cards = is_array( $oikos_args['cards'] ) ? $oikos_args['cards'] : oikos_get_service_cards();

if ( '' === $oikos_title || ! $oikos_cards ) {
	return;
}

$oikos_level    = min( 4, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_title_id = wp_unique_id( 'oikos-services-carousel-' );
$oikos_cta      = oikos_cta();
$oikos_button   = array(
	'text'   => null === $oikos_args['button_text'] ? oikos_service_card_button_text() : (string) $oikos_args['button_text'],
	'url'    => $oikos_cta['url'],
	'target' => $oikos_cta['target'],
);

$oikos_slides = array();
foreach ( array_values( $oikos_cards ) as $oikos_card ) {
	ob_start();
	get_template_part(
		'template-parts/components/therapy-card',
		null,
		array(
			'card'           => $oikos_card,
			'heading_level'  => $oikos_level + 1,
			'button'         => $oikos_button,
			'track_location' => $oikos_args['track_location'],
		)
	);
	$oikos_slides[] = (string) ob_get_clean();
}
?>
<section class="oikos-section oikos-services-carousel" aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>">
	<div class="oikos-container oikos-services-carousel__inner">
		<div class="oikos-services-carousel__content">
			<?php
			printf(
				'<h%1$d id="%2$s" class="oikos-services-carousel__title oikos-text-heading">%3$s</h%1$d>',
				(int) $oikos_level,
				esc_attr( $oikos_title_id ),
				esc_html( $oikos_title )
			);
			?>

			<?php if ( '' !== trim( (string) $oikos_args['text'] ) ) : ?>
				<p class="oikos-services-carousel__text oikos-text-body oikos-text-secondary"><?php echo wp_kses( $oikos_args['text'], array( 'br' => array() ) ); ?></p>
			<?php endif; ?>
		</div>

		<div class="oikos-services-carousel__media">
			<?php
			get_template_part(
				'template-parts/components/slider',
				null,
				array(
					'slides'     => $oikos_slides,
					'label'      => $oikos_title,
					'item_label' => __( 'terapia', 'oikos' ),
					'class'      => 'oikos-slider--cards oikos-slider--dots-below oikos-slider--on-light oikos-services-carousel__slider',
				)
			);
			?>
		</div>
	</div>
</section>
