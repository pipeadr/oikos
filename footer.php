<?php
/**
 * Pie del sitio.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_footer_menus = array(
	'footer-acompanamientos' => __( 'Acompañamientos', 'oikos' ),
	'footer-contenido'       => __( 'Contenido', 'oikos' ),
	'footer-oikos'           => __( 'Oikos', 'oikos' ),
);
?>
</main>

<footer class="oikos-footer">
	<?php foreach ( $oikos_footer_menus as $oikos_location => $oikos_label ) : ?>
		<?php if ( has_nav_menu( $oikos_location ) ) : ?>
			<nav class="oikos-footer__nav" aria-label="<?php echo esc_attr( $oikos_label ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => $oikos_location,
						'container'      => false,
						'menu_class'     => 'oikos-footer__menu',
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>
	<?php endforeach; ?>

	<p class="oikos-footer__copyright">
		&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
	</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
