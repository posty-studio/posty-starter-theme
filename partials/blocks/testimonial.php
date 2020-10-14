<div class="wp-block-posty-testimonial">
	<div class="wp-block-posty-testimonial__content">
		<?php if ( ! empty( $args['attributes']['content'] ) ) : ?>
			<?php echo wp_kses_post( $args['attributes']['content'] ); ?>
		<?php endif; ?>
	</div>
</div>
