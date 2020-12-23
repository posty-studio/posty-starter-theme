<?php

namespace Posty;

class Setup {
	private function set_constants(): void {
		define( 'POSTY_THEME_VERSION', '1.0.0' );
		define( 'POSTY_THEME_SLUG', 'posty-starter-theme' );
		define( 'POSTY_THEME_BLOCK_NAMESPACE', 'posty' );
		define( 'POSTY_THEME_PATH', get_template_directory() );
		define( 'POSTY_THEME_URL', get_template_directory_uri() );
		define( 'POSTY_THEME_ASSETS_PATH', POSTY_THEME_PATH . '/assets' );
		define( 'POSTY_THEME_ASSETS_URL', POSTY_THEME_URL . '/assets' );
		define( 'POSTY_THEME_TEMPLATES_PATH', POSTY_THEME_PATH . '/templates' );
		define( 'POSTY_THEME_LANGUAGES_PATH', POSTY_THEME_PATH . '/languages' );
	}

	public function init(): void {
		$this->set_constants();

		Assets::register();
		Blocks::register();
		Block_Editor::init();
		Cleanup::init();

		add_action( 'after_setup_theme', [ $this, 'add_theme_supports' ] );
	}

	public function add_theme_supports(): void {
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ] );
		add_theme_support( 'post-thumbnails' );

		add_image_size( 'posty-xlarge', 1920 );

		register_nav_menu( 'primary-menu', __( 'Primary Menu', 'posty-starter-theme' ) );
	}
}
