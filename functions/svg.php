<?php

namespace Posty;

/**
 * Return the content of an SVG file.
 *
 * @param string $name
 * @param array  $args
 * @return string|void
 */
function svg( string $name, array $args = [] ) : string {
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
