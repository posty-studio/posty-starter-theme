<?php

namespace Posty;

/**
 * Render a partial.
 *
 * @param string $name
 * @param array  $args
 * @param bool   $echo
 * @return string
 */
function render( string $name, array $args = [], $echo = true ) {
	ob_start();

	get_template_part( 'partials/' . $name, null, $args );

	$output = ob_get_clean();

	if ( ! $echo ) {
		return $output;
	}

	echo $output; // @phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
