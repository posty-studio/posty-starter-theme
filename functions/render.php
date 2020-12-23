<?php

namespace Posty;

/**
 * Render a partial.
 *
 * @param array  $args
 */
function render( string $name, array $args = [], bool $echo = true ): string {
	ob_start();

	get_template_part( 'partials/' . $name, null, $args );

	$output = ob_get_clean();

	if ( ! $echo ) {
		return $output;
	}

	echo $output; // @phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
