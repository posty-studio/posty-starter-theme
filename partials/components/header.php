<div class="wrapper">
	<header class="header">
		<a class="header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo \Posty\svg( 'logo' ); // @phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>

		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<?php
			wp_nav_menu(
				[
					'menu' => 'primary',
				]
			);
			?>
		<?php endif; ?>
	</header>
</div>
