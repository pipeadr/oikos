<?php
/**
 * Sección: pasos numerados ("Empezar es más simple", "Cómo funciona",
 * "Así es una sesión"…). Reutilizable en Inicio, Servicios y páginas de terapia.
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 *
 * Args:
 * - layout         (string) Disposición. Hoy: 'split' (intro a la izquierda,
 *                           pasos a la derecha). Para agregar otra: sumarla a
 *                           $oikos_layouts y estilar .oikos-steps--layout-<nombre>.
 * - background     (string) 'none' (transparente, hereda el fondo del
 *                           contenedor) | 'cream' | 'white'.
 * - eyebrow        (string) Antetítulo opcional.
 * - title          (string) Título (obligatorio junto con al menos un paso).
 * - heading_level  (int)    2–4. Los títulos de paso van un nivel más abajo.
 * - steps          (array)  Lista de array{title:string,text:string}.
 * - link           (array)  url, title, target. Opcional.
 * - button_variant (string) 'primary' | 'secondary'.
 * - track          (string) data-track del botón.
 * - track_location (string) data-track-location del botón.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'layout'         => 'split',
		'background'     => 'none',
		'eyebrow'        => '',
		'title'          => '',
		'heading_level'  => 2,
		'steps'          => array(),
		'link'           => array(),
		'button_variant' => 'primary',
		'track'          => '',
		'track_location' => '',
	)
);

$oikos_title = trim( (string) $oikos_args['title'] );

$oikos_steps = array();
foreach ( (array) $oikos_args['steps'] as $oikos_step ) {
	$oikos_step_title = isset( $oikos_step['title'] ) ? trim( (string) $oikos_step['title'] ) : '';

	if ( '' !== $oikos_step_title ) {
		$oikos_steps[] = array(
			'title' => $oikos_step_title,
			'text'  => isset( $oikos_step['text'] ) ? trim( (string) $oikos_step['text'] ) : '',
		);
	}
}

if ( '' === $oikos_title || ! $oikos_steps ) {
	return;
}

// Disposiciones disponibles (la primera es la de respaldo).
$oikos_layouts = array( 'split' );
$oikos_layout  = in_array( $oikos_args['layout'], $oikos_layouts, true ) ? $oikos_args['layout'] : $oikos_layouts[0];

$oikos_bg       = in_array( $oikos_args['background'], array( 'none', 'cream', 'white' ), true ) ? $oikos_args['background'] : 'none';
$oikos_variant  = 'secondary' === $oikos_args['button_variant'] ? 'secondary' : 'primary';
$oikos_level    = min( 4, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_link     = is_array( $oikos_args['link'] ) ? $oikos_args['link'] : array();
$oikos_title_id = wp_unique_id( 'oikos-steps-' );

$oikos_classes = sprintf(
	'oikos-section oikos-steps oikos-steps--layout-%1$s oikos-steps--bg-%2$s',
	$oikos_layout,
	$oikos_bg
);
?>
<section class="<?php echo esc_attr( $oikos_classes ); ?>" aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>">
	<div class="oikos-container oikos-steps__inner">
		<div class="oikos-steps__intro">
			<?php if ( '' !== trim( (string) $oikos_args['eyebrow'] ) ) : ?>
				<p class="oikos-steps__eyebrow oikos-text-eyebrow"><?php echo esc_html( $oikos_args['eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php
			printf(
				'<h%1$d id="%2$s" class="oikos-steps__heading oikos-text-heading">%3$s</h%1$d>',
				(int) $oikos_level,
				esc_attr( $oikos_title_id ),
				esc_html( $oikos_title )
			);
			?>
		</div>

		<div class="oikos-steps__body">
			<ol class="oikos-steps__list">
				<?php foreach ( $oikos_steps as $oikos_index => $oikos_step ) : ?>
					<li class="oikos-steps__item">
						<span class="oikos-steps__number oikos-text-eyebrow" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $oikos_index + 1 ) ); ?></span>

						<?php
						printf(
							'<h%1$d class="oikos-steps__title oikos-text-step">%2$s</h%1$d>',
							(int) $oikos_level + 1,
							esc_html( $oikos_step['title'] )
						);
						?>

						<?php if ( '' !== $oikos_step['text'] ) : ?>
							<p class="oikos-steps__text oikos-text-body"><?php echo nl2br( esc_html( $oikos_step['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapado con esc_html(). ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>

			<?php if ( ! empty( $oikos_link['url'] ) && ! empty( $oikos_link['title'] ) ) : ?>
				<a
					class="<?php echo esc_attr( 'oikos-btn oikos-btn--' . $oikos_variant . ' oikos-btn--block-mobile oikos-steps__cta' ); ?>"
					href="<?php echo esc_url( $oikos_link['url'] ); ?>"
					<?php echo ( isset( $oikos_link['target'] ) && '_blank' === $oikos_link['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
					<?php if ( '' !== $oikos_args['track'] ) : ?>
						data-track="<?php echo esc_attr( $oikos_args['track'] ); ?>"
						data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
					<?php endif; ?>
				><?php echo esc_html( $oikos_link['title'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
