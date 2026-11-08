<?php
/**
 * Footer: banda newsletter, bloque café (CTA, redes, columnas) y franja legal.
 *
 * Grilla del bloque café (áreas):
 * - Desktop: cta | cols / social | cols
 * - Tablet:  cta / cols (2×2) / social
 * - Móvil:   cta / cols (apiladas) / social
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_cta       = oikos_cta();
$oikos_phone     = trim( (string) oikos_option( 'oikos_global_telefono' ) );
$oikos_email     = sanitize_email( (string) oikos_option( 'oikos_global_correo' ) );
$oikos_address   = (string) oikos_option( 'oikos_global_direccion' );
$oikos_contact_h = (string) oikos_option( 'oikos_global_contacto_titulo', __( 'Contacto', 'oikos' ) );
$oikos_has_info  = '' !== $oikos_phone || '' !== $oikos_email || '' !== trim( $oikos_address );
?>
<footer class="oikos-footer">
	<?php get_template_part( 'template-parts/layout/newsletter' ); ?>

	<div class="oikos-footer__main">
		<div class="oikos-container oikos-footer__grid">
			<div class="oikos-footer__cta">
				<?php if ( '' !== $oikos_cta['footer_title'] ) : ?>
					<h2 class="oikos-footer__cta-title"><?php echo esc_html( $oikos_cta['footer_title'] ); ?></h2>
				<?php endif; ?>
				<?php
				oikos_the_cta_button(
					array(
						'text'     => $oikos_cta['footer_text'],
						'class'    => 'oikos-btn oikos-btn--tonal oikos-btn--block-mobile',
						'location' => 'footer',
					)
				);
				?>
			</div>

			<?php get_template_part( 'template-parts/components/social-links', null, array( 'location' => 'footer', 'class' => 'oikos-footer__social' ) ); ?>

			<div class="oikos-footer__cols">
				<?php
				foreach ( array( 'footer-acompanamientos', 'footer-contenido', 'footer-oikos' ) as $oikos_location ) {
					oikos_footer_menu( $oikos_location );
				}
				?>

				<?php if ( $oikos_has_info ) : ?>
					<div class="oikos-footer__col">
						<h2 class="oikos-footer__col-title"><?php echo esc_html( $oikos_contact_h ); ?></h2>
						<address class="oikos-footer__address">
							<ul class="oikos-footer__links">
								<?php if ( '' !== $oikos_phone && '' !== oikos_phone_href( $oikos_phone ) ) : ?>
									<li><a href="<?php echo esc_url( oikos_phone_href( $oikos_phone ), array( 'tel' ) ); ?>" data-track="telefono" data-track-location="footer"><?php echo esc_html( $oikos_phone ); ?></a></li>
								<?php endif; ?>
								<?php if ( '' !== $oikos_email ) : ?>
									<li><a href="<?php echo esc_url( 'mailto:' . $oikos_email, array( 'mailto' ) ); ?>" data-track="correo" data-track-location="footer"><?php echo esc_html( $oikos_email ); ?></a></li>
								<?php endif; ?>
								<?php if ( '' !== trim( $oikos_address ) ) : ?>
									<li class="oikos-footer__address-text"><?php echo wp_kses( $oikos_address, array( 'br' => array() ) ); ?></li>
								<?php endif; ?>
							</ul>
						</address>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="oikos-footer__legal">
		<div class="oikos-container oikos-footer__legal-inner">
			<?php if ( has_nav_menu( 'footer-legal' ) ) : ?>
				<nav class="oikos-footer__legal-nav" aria-label="<?php esc_attr_e( 'Legal', 'oikos' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-legal',
							'container'      => false,
							'menu_class'     => 'oikos-footer__legal-links',
							'fallback_cb'    => false,
							'depth'          => 1,
						)
					);
					?>
				</nav>
			<?php endif; ?>

			<p class="oikos-footer__copyright">
				<?php
				/* translators: 1: año actual, 2: nombre de la marca. */
				printf( esc_html__( '%1$s © %2$s', 'oikos' ), esc_html( wp_date( 'Y' ) ), esc_html( oikos_brand_name() ) );
				?>
			</p>
		</div>
	</div>
</footer>
