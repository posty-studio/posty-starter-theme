<?php

namespace Posty;

/**
 * Conditionally join classes together.
 *
 * Based on https://github.com/cstro/classnames-php.
 *
 * @param string|array
 */
function classes(): string {
	$args = func_get_args();
	$data = array_reduce( $args, function ( $result, $arg ) {
		if ( is_array( $arg ) ) {
			return array_merge( $result, $arg );
		}

		$result[] = $arg;

		return $result;
	}, [] );

	$classes = array_map( function ( $key, $value ) {
		$condition = is_int( $key ) ? null : $value;
		$return    = is_int( $key ) ? $value : $key;

		$is_stringable_type   = ! is_array( $return ) && ! is_object( $return );
		$is_stringable_object = is_object( $return ) && method_exists( $return, '__toString' );

		if ( ! $is_stringable_type && ! $is_stringable_object ) {
			return null;
		}

		if ( $condition === null ) {
			return $return;
		}

		return $condition ? $return : null;
	}, array_keys( $data ), array_values( $data ) );

	return implode( ' ', array_filter( $classes ) );
}

/**
 * Render a partial.
 *
 * @return string|false|void
 */
function render( string $name, array $args = [], bool $echo = true ) {
	ob_start();

	get_template_part( 'partials/' . $name, null, $args );

	$output = ob_get_clean();

	if ( ! $echo ) {
		return $output;
	}

	echo $output; // @phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Return the content of an SVG file.
 *
 * @param array  $args
 * @return string|void
 */
function svg( string $name, array $args = [] ): string {
	$path = POSTY_THEME_ASSETS_PATH . '/img/' . $name . '.svg';

	if ( ! file_exists( $path ) ) {
		return false;
	}

	// @phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$doc = new \DOMDocument();
	$doc->loadXML( file_get_contents( $path ) );
	$doc->documentElement->setAttribute( 'role', 'img' );

	$unique_id  = uniqid();
	$labels     = [
		'title' => $args['title'] ?? '',
		'desc'  => $args['description'] ?? '',
	];
	$labelledby = [];

	foreach ( $labels as $tag => $label ) {
		if ( empty( $label ) ) {
			continue;
		}

		$id      = $tag . '-' . $unique_id;
		$element = $doc->createElement( $tag, $label );
		$element->setAttribute( 'id', $id );
		$doc->firstChild->appendChild( $element );
		$labelledby[] = $id;
	}

	if ( ! empty( $labelledby ) ) {
		$doc->documentElement->setAttribute( 'aria-labelledby', join( ' ', $labelledby ) );
	} else {
		$doc->documentElement->setAttribute( 'aria-hidden', 'true' );
	}

	return $doc->saveXML( $doc->documentElement );
	// @phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
}
