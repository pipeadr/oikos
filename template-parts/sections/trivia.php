<?php
/**
 * Sección: trivia "Mitos y verdades" (Servicios general).
 *
 * Columna izquierda: eyebrow + título. Columna derecha: un mazo de cartas;
 * la de arriba es la pregunta activa y detrás asoman (máx. 4) las que
 * faltan. Cada carta tiene dos caras que comparten celda: la pregunta
 * (contador, reloj, afirmación, Verdad / Mito) y la respuesta (respuesta
 * correcta, explicación, Continúa). Al final, una carta azul con el CTA
 * global y "Repite la trivia". La lógica vive en assets/js/trivia.js.
 *
 * Un solo DOM sirve para todo: sin JS, los controles y la carta final llegan
 * con `hidden` y queda una lista legible (e indexable) de afirmaciones con
 * su respuesta y explicación. El JS marca .is-ready y arma el mazo.
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 *
 * Args:
 * - id             (string) Prefijo único para los id del HTML.
 * - eyebrow        (string) Texto pequeño sobre el título. Opcional.
 * - title          (string) Título (estilo Heading). Vacío → no se imprime.
 * - heading_level  (int)    Nivel del título (2 por defecto); las afirmaciones van un nivel debajo.
 * - seconds        (int)    Segundos por pregunta (0 = sin temporizador; máx. 60).
 * - items          (array)  Cada una: statement, answer ('verdad' | 'mito'), explanation.
 * - final          (array)  title, text, restart_text de la carta final (vacíos → textos por defecto).
 * - cta            (array)  Enlace del botón final (url, title, target). null → CTA global.
 * - track_location (string) data-track-location del botón de agendar y de los eventos del dataLayer.
 *
 * Menos de 2 preguntas válidas → no se imprime.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'id'             => 'trivia',
		'eyebrow'        => '',
		'title'          => '',
		'heading_level'  => 2,
		'seconds'        => 20,
		'items'          => array(),
		'final'          => array(),
		'cta'            => null,
		'track_location' => 'trivia',
	)
);

// Preguntas válidas (afirmación y respuesta) y normalizadas. Las comillas las
// pone el tema: se quitan las que el editor haya escrito.
$oikos_items = array();
foreach ( (array) $oikos_args['items'] as $oikos_item ) {
	if ( ! is_array( $oikos_item ) ) {
		continue;
	}

	$oikos_statement = trim( (string) ( $oikos_item['statement'] ?? '' ), " \t\n\r\0\x0B\"“”«»'" );
	$oikos_answer    = strtolower( trim( (string) ( $oikos_item['answer'] ?? '' ) ) );

	if ( '' === $oikos_statement || ! in_array( $oikos_answer, array( 'verdad', 'mito' ), true ) ) {
		continue;
	}

	$oikos_items[] = array(
		'statement'   => $oikos_statement,
		'answer'      => $oikos_answer,
		'explanation' => trim( (string) ( $oikos_item['explanation'] ?? '' ) ),
	);
}

if ( '' === trim( (string) $oikos_args['title'] ) || count( $oikos_items ) < 2 ) {
	return;
}

$oikos_id       = sanitize_html_class( $oikos_args['id'], 'trivia' );
$oikos_level    = min( 5, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_sublevel = $oikos_level + 1;
$oikos_title_id = $oikos_id . '-titulo';
$oikos_seconds  = min( 60, max( 0, (int) $oikos_args['seconds'] ) );
$oikos_total    = count( $oikos_items );
$oikos_eyebrow  = trim( (string) $oikos_args['eyebrow'] );

$oikos_final = wp_parse_args(
	array_filter( array_map( 'trim', array_map( 'strval', (array) $oikos_args['final'] ) ) ),
	array(
		'title'        => __( '¡Has finalizado!', 'oikos' ),
		'text'         => __( 'Completaste las preguntas de la trivia; mientras tanto nosotros nos estaremos preparando para cuando sientas que todo está claro para ti.', 'oikos' ),
		'restart_text' => __( 'Repite la trivia', 'oikos' ),
	)
);

$oikos_cta = is_array( $oikos_args['cta'] ) ? oikos_link_or_cta( $oikos_args['cta'] ) : oikos_link_or_cta( array() );

$oikos_labels = array(
	'verdad' => __( 'Verdad', 'oikos' ),
	'mito'   => __( 'Mito', 'oikos' ),
);

// Textos del JS (anuncios de la región viva).
$oikos_strings = array(
	/* translators: %s: la respuesta correcta en frase ("un mito" / "verdad"). */
	'correct'   => __( 'Correcto: es %s.', 'oikos' ),
	/* translators: %s: la respuesta correcta en frase ("un mito" / "verdad"). */
	'incorrect' => __( 'Incorrecto: es %s.', 'oikos' ),
	/* translators: %s: la respuesta correcta en frase ("un mito" / "verdad"). */
	'timeout'   => __( 'Se acabó el tiempo: es %s.', 'oikos' ),
	'ending'    => __( 'Quedan 5 segundos.', 'oikos' ),
	'verdad'    => __( 'verdad', 'oikos' ),
	'mito'      => __( 'un mito', 'oikos' ),
);
?>
<section
	class="oikos-trivia"
	id="<?php echo esc_attr( $oikos_id ); ?>"
	aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>"
	data-trivia
	data-seconds="<?php echo (int) $oikos_seconds; ?>"
	data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
	data-strings="<?php echo esc_attr( wp_json_encode( $oikos_strings ) ); ?>"
>
	<div class="oikos-container oikos-trivia__inner">
		<div class="oikos-trivia__intro">
			<?php if ( '' !== $oikos_eyebrow ) : ?>
				<p class="oikos-trivia__eyebrow oikos-text-eyebrow"><?php echo esc_html( $oikos_eyebrow ); ?></p>
			<?php endif; ?>

			<h<?php echo (int) $oikos_level; ?> id="<?php echo esc_attr( $oikos_title_id ); ?>" class="oikos-trivia__heading oikos-text-heading"><?php echo esc_html( $oikos_args['title'] ); ?></h<?php echo (int) $oikos_level; ?>>
		</div>

		<div class="oikos-trivia__game">
			<ol class="oikos-trivia__deck" role="list">
				<?php foreach ( $oikos_items as $oikos_index => $oikos_item ) : ?>
					<li class="oikos-trivia__card" data-answer="<?php echo esc_attr( $oikos_item['answer'] ); ?>">
						<div class="oikos-trivia__face oikos-trivia__face--question">
							<div class="oikos-trivia__meta" hidden>
								<p class="oikos-trivia__count">
									<?php
									printf(
										/* translators: 1: número de la pregunta, 2: total de preguntas. */
										esc_html__( '%1$s de %2$s', 'oikos' ),
										'<span class="screen-reader-text">' . esc_html__( 'Pregunta', 'oikos' ) . ' </span>' . (int) ( $oikos_index + 1 ),
										(int) $oikos_total
									);
									?>
								</p>

								<?php if ( $oikos_seconds > 0 ) : ?>
									<p class="oikos-trivia__timer">
										<?php oikos_the_icon( 'clock', 'oikos-trivia__clock' ); ?>
										<span class="oikos-trivia__seconds" aria-hidden="true"><?php echo esc_html( $oikos_seconds . 's' ); ?></span>
										<span class="screen-reader-text">
											<?php
											/* translators: %d: segundos por pregunta. */
											echo esc_html( sprintf( _n( 'Tienes %d segundo para responder.', 'Tienes %d segundos para responder.', $oikos_seconds, 'oikos' ), $oikos_seconds ) );
											?>
										</span>
									</p>
								<?php endif; ?>
							</div>

							<h<?php echo (int) $oikos_sublevel; ?> class="oikos-trivia__statement" tabindex="-1">
								<?php
								/* translators: %s: afirmación de la trivia (va entre comillas). */
								echo esc_html( sprintf( __( '“%s”', 'oikos' ), $oikos_item['statement'] ) );
								?>
							</h<?php echo (int) $oikos_sublevel; ?>>

							<div class="oikos-trivia__choices" hidden>
								<button type="button" class="oikos-btn oikos-btn--primary oikos-trivia__choice" data-choice="verdad"><?php echo esc_html( $oikos_labels['verdad'] ); ?></button>
								<button type="button" class="oikos-btn oikos-btn--secondary oikos-trivia__choice" data-choice="mito"><?php echo esc_html( $oikos_labels['mito'] ); ?></button>
							</div>
						</div>

						<div class="oikos-trivia__face oikos-trivia__face--answer">
							<p class="oikos-trivia__timeout" hidden><?php esc_html_e( 'Se acabó el tiempo', 'oikos' ); ?></p>
							<p class="oikos-trivia__verdict" tabindex="-1"><span class="screen-reader-text"><?php esc_html_e( 'Respuesta:', 'oikos' ); ?> </span><?php echo esc_html( $oikos_labels[ $oikos_item['answer'] ] ); ?></p>

							<?php if ( '' !== $oikos_item['explanation'] ) : ?>
								<p class="oikos-trivia__explanation"><?php echo esc_html( $oikos_item['explanation'] ); ?></p>
							<?php endif; ?>

							<button type="button" class="oikos-btn oikos-btn--light oikos-trivia__next" hidden><?php esc_html_e( 'Continúa', 'oikos' ); ?></button>
						</div>
					</li>
				<?php endforeach; ?>

				<li class="oikos-trivia__card oikos-trivia__card--final" hidden>
					<div class="oikos-trivia__face">
						<h<?php echo (int) $oikos_sublevel; ?> class="oikos-trivia__verdict oikos-trivia__final-title" tabindex="-1"><?php echo esc_html( $oikos_final['title'] ); ?></h<?php echo (int) $oikos_sublevel; ?>>
						<p class="oikos-trivia__explanation"><?php echo esc_html( $oikos_final['text'] ); ?></p>

						<div class="oikos-trivia__actions">
							<?php if ( '' !== $oikos_cta['url'] ) : ?>
								<a
									class="oikos-btn oikos-btn--light"
									href="<?php echo esc_url( $oikos_cta['url'] ); ?>"
									data-track="agenda-cita"
									data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
									<?php echo '_blank' === $oikos_cta['target'] ? 'target="_blank" rel="noopener"' : ''; ?>
								><?php echo esc_html( $oikos_cta['title'] ); ?></a>
							<?php endif; ?>

							<button type="button" class="oikos-btn oikos-btn--light oikos-trivia__restart"><?php echo esc_html( $oikos_final['restart_text'] ); ?></button>
						</div>
					</div>
				</li>
			</ol>

			<p class="screen-reader-text" aria-live="polite" data-trivia-live></p>
		</div>
	</div>
</section>
