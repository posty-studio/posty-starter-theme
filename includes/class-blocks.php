<?php

namespace Posty;

class Blocks {
	public static function register() {
		self::register_block( 'testimonial' );
	}

	/**
	 * Register a server-side block.
	 *
	 * @param string $name
	 */
	public static function register_block( $name ) {
		if ( empty( $name ) || ! is_string( $name ) ) {
			return;
		}

		register_block_type(
			POSTY_THEME_BLOCK_NAMESPACE . '/' . $name,
			[
				'render_callback' => function ( $attributes, $content ) use ( $name ) {
					return render(
						'blocks/' . $name,
						[
							'attributes' => $attributes,
							'content'    => $content,
						]
					);
				},
			]
		);
	}
}
