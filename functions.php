<?php

spl_autoload_register(
	function ( $class ): void {
		if ( strpos( $class, 'Posty\\' ) !== 0 ) {
			return;
		}

		$file = str_replace( 'Posty\\', '', $class );
		$file = strtolower( $file );
		$file = str_replace( '_', '-', $file );

		/* Convert sub-namespaces into directories */
		$path = explode( '\\', $file );
		$file = array_pop( $path );
		$path = implode( '/', $path );

		require_once __DIR__ . '/includes/' . $path . '/class-' . $file . '.php';
	}
);

require_once __DIR__ . '/includes/template-tags.php';

$setup = new Posty\Setup();
$setup->init();
