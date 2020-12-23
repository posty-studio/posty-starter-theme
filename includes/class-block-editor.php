<?php

namespace Posty;

class Block_Editor {
	public static function init(): void {
		add_action( 'after_setup_theme', [ __CLASS__, 'block_editor_support' ] );
	}

	public static function block_editor_support(): void {
		add_theme_support( 'align-wide' );
		add_theme_support( 'disable-custom-colors' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'disable-custom-font-sizes' );
	}
}
