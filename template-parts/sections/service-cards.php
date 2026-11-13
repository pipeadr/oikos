<?php
/**
 * Sección: tarjetas de acompañamiento (tarjetas grandes de color, inclinadas,
 * que enlazan a cada servicio).
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 *
 * Args:
 * - title          (string) Título (obligatorio junto con al menos una tarjeta).
 * - subtitle       (string) Texto corto bajo el título. Opcional.
 * - heading_level  (int)    2–4. Los títulos de tarjeta van un nivel más abajo.
 * - cards          (array)  Lista de array{url:string,text:string,color:string};
 *                           color: 'arena' | 'cafe' | 'verde'.
 * - link           (array)  url, title, target del botón. Opcional.
 * - track_location (string) data-track-location de tarjetas y botón.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'title'          => '',
		'subtitle'       => '',
		'heading_level'  => 2,
		'cards'          => array(),
		'link'           => array(),
		'track_location' => '',
	)
);

$oikos_title = trim( (string) $oikos_args['title'] );

$oikos_cards = array();
foreach ( (array) $oikos_args['cards'] as $oikos_card ) {
	if ( empty( $oikos_card['url'] ) || empty( $oikos_card['text'] ) ) {
		continue;
	}

	$oikos_cards[] = array(
		'url'   => (string) $oikos_card['url'],
		'text'  => (string) $oikos_card['text'],
		'color' => isset( $oikos_card['color'] ) && in_array( $oikos_card['color'], array( 'arena', 'cafe', 'verde' ), true ) ? $oikos_card['color'] : 'arena',
	);
}

if ( '' === $oikos_title || ! $oikos_cards ) {
	return;
}

$oikos_level    = min( 4, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_link     = is_array( $oikos_args['link'] ) ? $oikos_args['link'] : array();
$oikos_title_id = wp_unique_id( 'oikos-service-cards-' );
$oikos_location = (string) $oikos_args['track_location'];
?>
<section class="oikos-section oikos-service-cards" aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>">
	<div class="oikos-container oikos-service-cards__inner">
		<div class="oikos-service-cards__header">
			<?php
			printf(
				'<h%1$d id="%2$s" class="oikos-service-cards__title oikos-text-heading">%3$s</h%1$d>',
				(int) $oikos_level,
				esc_attr( $oikos_title_id ),
				esc_html( $oikos_title )
			);
			?>

			<?php if ( '' !== trim( (string) $oikos_args['subtitle'] ) ) : ?>
				<p class="oikos-service-cards__subtitle oikos-text-eyebrow"><?php echo esc_html( $oikos_args['subtitle'] ); ?></p>
			<?php endif; ?>
		</div>

		<ul class="oikos-service-cards__list">
			<?php foreach ( $oikos_cards as $oikos_card ) : ?>
				<li class="oikos-service-cards__item">
					<a
						class="<?php echo esc_attr( 'oikos-service-card oikos-service-card--' . $oikos_card['color'] ); ?>"
						href="<?php echo esc_url( $oikos_card['url'] ); ?>"
						data-track="servicio-card"
						data-track-location="<?php echo esc_attr( $oikos_location ); ?>"
					>
						<?php
						printf(
							'<h%1$d class="oikos-service-card__title oikos-text-card">%2$s</h%1$d>',
							(int) $oikos_level + 1,
							esc_html( $oikos_card['text'] )
						);
						?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( ! empty( $oikos_link['url'] ) && ! empty( $oikos_link['title'] ) ) : ?>
			<a
				class="oikos-btn oikos-btn--primary oikos-btn--block-mobile oikos-service-cards__cta"
				href="<?php echo esc_url( $oikos_link['url'] ); ?>"
				<?php echo ( isset( $oikos_link['target'] ) && '_blank' === $oikos_link['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
				data-track="ver-servicios"
				data-track-location="<?php echo esc_attr( $oikos_location ); ?>"
			><?php echo esc_html( $oikos_link['title'] ); ?></a>
		<?php endif; ?>
	</div>
</section>
