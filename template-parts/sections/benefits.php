<?php
/**
 * Sección: beneficios con foto que cambia ("¿Por qué asistir a terapia?").
 *
 * Lista de beneficios a la izquierda (número, título y descripción) y una
 * foto vertical a la derecha. Un beneficio está activo (título verde) y la
 * foto muestra el suyo; al elegir otro, la foto cambia con un fundido. La
 * rotación automática la maneja assets/js/benefits.js.
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 *
 * Args:
 * - id             (string) Prefijo único para los id del HTML.
 * - title          (string) Título de la sección (estilo Heading, menor que los beneficios). Vacío → no se imprime.
 * - heading_level  (int)    Nivel del título (2 por defecto); los beneficios van un nivel debajo.
 * - items          (array)  Cada uno: title, text, image_id, focus_x, focus_y (0–100).
 * - autoplay       (bool)   Rotación automática.
 * - interval       (int)    Segundos por beneficio (4–20).
 *
 * Sin JS: el primer beneficio y su foto llegan activos y todo el texto está
 * en el HTML. Un beneficio sin foto conserva la del anterior; si el primero
 * no tiene, se ve una superficie arena del mismo tamaño. Sin ninguna foto, la
 * sección queda en una columna.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'id'            => 'beneficios',
		'title'         => '',
		'heading_level' => 2,
		'items'         => array(),
		'autoplay'      => true,
		'interval'      => 7,
	)
);

// Beneficios válidos (con título) y normalizados.
$oikos_items = array();
foreach ( (array) $oikos_args['items'] as $oikos_item ) {
	if ( ! is_array( $oikos_item ) || '' === trim( (string) ( $oikos_item['title'] ?? '' ) ) ) {
		continue;
	}
	$oikos_items[] = array(
		'title'    => trim( (string) $oikos_item['title'] ),
		'text'     => trim( (string) ( $oikos_item['text'] ?? '' ) ),
		'image_id' => absint( $oikos_item['image_id'] ?? 0 ),
		'focus_x'  => min( 100, max( 0, (int) ( $oikos_item['focus_x'] ?? 50 ) ) ),
		'focus_y'  => min( 100, max( 0, (int) ( $oikos_item['focus_y'] ?? 50 ) ) ),
	);
}

if ( '' === trim( (string) $oikos_args['title'] ) || ! $oikos_items ) {
	return;
}

$oikos_id       = sanitize_html_class( $oikos_args['id'], 'beneficios' );
$oikos_level    = min( 5, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_sublevel = $oikos_level + 1;
$oikos_interval = min( 20, max( 4, (int) $oikos_args['interval'] ) );
$oikos_autoplay = (bool) $oikos_args['autoplay'] && count( $oikos_items ) > 1;
$oikos_media_id = $oikos_id . '-foto';

// Fotos: una por beneficio que la tenga. Cada beneficio apunta a la suya o a
// la del último anterior que tenga (-1 = ninguna todavía → superficie arena).
$oikos_photos = array();
$oikos_last   = -1;
foreach ( $oikos_items as $oikos_index => $oikos_item ) {
	$oikos_img = '';

	if ( $oikos_item['image_id'] ) {
		$oikos_img = wp_get_attachment_image(
			$oikos_item['image_id'],
			'oikos-portrait',
			false,
			array(
				'class'    => 'oikos-benefits__photo' . ( 0 === $oikos_index ? ' is-active' : '' ),
				// Columna: contenedor − márgenes (móvil) · 2/5 del contenedor (tablet) · 523 (desktop).
				'sizes'    => '(max-width: 47.9375rem) calc(100vw - 2.5rem), (max-width: 63.9375rem) 38vw, min(32.6875rem, 45vw)',
				'loading'  => 0 === $oikos_index ? false : 'lazy',
				'decoding' => 'async',
				'style'    => sprintf( 'object-position: %d%% %d%%', $oikos_item['focus_x'], $oikos_item['focus_y'] ),
				'data-photo-index' => (string) $oikos_index,
			)
		);
	}

	if ( '' !== $oikos_img ) {
		$oikos_photos[ $oikos_index ] = $oikos_img;
		$oikos_last                   = $oikos_index;
	}

	$oikos_items[ $oikos_index ]['photo'] = $oikos_last;
}

$oikos_has_media = (bool) $oikos_photos;
$oikos_classes   = 'oikos-benefits' . ( $oikos_has_media ? '' : ' oikos-benefits--no-media' );
$oikos_title_id  = $oikos_id . '-titulo';
?>
<section
	class="<?php echo esc_attr( $oikos_classes ); ?>"
	aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>"
	data-benefits
	<?php if ( $oikos_autoplay ) : ?>
		data-autoplay
		data-interval="<?php echo esc_attr( (string) $oikos_interval ); ?>"
		style="--benefits-interval: <?php echo esc_attr( (string) $oikos_interval ); ?>s"
	<?php endif; ?>
>
	<div class="oikos-container oikos-benefits__inner">
		<h<?php echo (int) $oikos_level; ?> id="<?php echo esc_attr( $oikos_title_id ); ?>" class="oikos-benefits__heading oikos-text-heading"><?php echo esc_html( $oikos_args['title'] ); ?></h<?php echo (int) $oikos_level; ?>>

		<?php if ( $oikos_has_media ) : ?>
			<div class="oikos-benefits__media" id="<?php echo esc_attr( $oikos_media_id ); ?>">
				<?php if ( -1 === $oikos_items[0]['photo'] ) : ?>
					<span class="oikos-benefits__photo oikos-benefits__placeholder is-active" data-photo-index="-1" aria-hidden="true"></span>
				<?php endif; ?>

				<?php
				foreach ( $oikos_photos as $oikos_img ) {
					echo $oikos_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapado en wp_get_attachment_image().
				}
				?>

				<?php if ( $oikos_autoplay ) : ?>
					<button
						type="button"
						class="oikos-benefits__toggle"
						aria-controls="<?php echo esc_attr( $oikos_media_id ); ?>"
						aria-label="<?php esc_attr_e( 'Pausar la rotación automática', 'oikos' ); ?>"
						data-label-pause="<?php esc_attr_e( 'Pausar la rotación automática', 'oikos' ); ?>"
						data-label-play="<?php esc_attr_e( 'Reanudar la rotación automática', 'oikos' ); ?>"
						hidden
					>
						<?php oikos_the_icon( 'pause', 'oikos-benefits__icon-pause' ); ?>
						<?php oikos_the_icon( 'play', 'oikos-benefits__icon-play' ); ?>
					</button>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<ol class="oikos-benefits__list" role="list">
			<?php foreach ( $oikos_items as $oikos_index => $oikos_item ) : ?>
				<li class="oikos-benefits__item<?php echo 0 === $oikos_index ? ' is-active' : ''; ?>" data-photo="<?php echo esc_attr( (string) $oikos_item['photo'] ); ?>">
					<span class="oikos-benefits__number oikos-text-eyebrow" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $oikos_index + 1 ) ); ?></span>

					<h<?php echo (int) $oikos_sublevel; ?> class="oikos-benefits__title oikos-text-heading-lg">
						<button
							type="button"
							class="oikos-benefits__button"
							<?php echo 0 === $oikos_index ? 'aria-current="true"' : ''; ?>
							<?php echo $oikos_has_media ? 'aria-controls="' . esc_attr( $oikos_media_id ) . '"' : ''; ?>
						><?php echo esc_html( $oikos_item['title'] ); ?></button>
					</h<?php echo (int) $oikos_sublevel; ?>>

					<?php if ( '' !== $oikos_item['text'] ) : ?>
						<p class="oikos-benefits__text oikos-text-body oikos-text-secondary"><?php echo esc_html( $oikos_item['text'] ); ?></p>
					<?php endif; ?>

					<?php if ( $oikos_autoplay ) : ?>
						<span class="oikos-benefits__progress" aria-hidden="true"></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
