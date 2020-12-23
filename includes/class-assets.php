<?php

namespace Posty;

class Assets {
	public static function register(): void {
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
		add_filter( 'style_loader_src', [ __CLASS__, 'remove_ver_query_arg' ], 10, 2 );
	}

	/**
	 * Remove version from styles.
	 */
	public static function remove_ver_query_arg( string $src ): string {
		return remove_query_arg( 'ver', $src );
	}

	/**
	 * Get the hashed filename of a CSS file.
	 */
	private static function get_css_filename( string $name ): string {
		$map      = POSTY_THEME_PATH . '/assets/manifest.php';
		$manifest = file_exists( $map ) ? require $map : [];

		return $manifest[ $name ] ?? $name;
	}

	/**
	 * Registers and enqueues a style.
	 *
	 * @param array  $dependencies
	 */
	private static function add_style( string $name, array $dependencies = [] ): void {
        // @phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_style(
			"posty-{$name}-style",
			POSTY_THEME_ASSETS_URL . '/css/' . self::get_css_filename( $name . '.css' ),
			$dependencies
		);
	}

	/**
	 * Registers and enqueues a script.
	 *
	 * @param array  $l10n
	 * @param array  $dependencies
	 */
	private static function add_script( string $name, array $l10n = [], array $dependencies = [] ): void {
		$asset_filepath = POSTY_THEME_ASSETS_PATH . '/js/' . $name . '.asset.php';
		$asset_file     = file_exists( $asset_filepath ) ? include $asset_filepath : [
			'dependencies' => [],
			'version'      => POSTY_THEME_VERSION,
		];

		wp_register_script(
			"posty-{$name}-script",
			POSTY_THEME_ASSETS_URL . '/js/' . $name . '.js',
			array_merge( $asset_file['dependencies'], $dependencies ),
			$asset_file['version'],
			true
		);

		if ( ! empty( $l10n ) && is_array( $l10n ) ) {
			wp_localize_script( "posty-{$name}-script", 'posty', $l10n );
		}

		wp_enqueue_script( "posty-{$name}-script" );
	}

	public static function enqueue_assets(): void {
		self::add_style( 'style' );
		self::add_script( 'app' );
	}

	public static function enqueue_admin_assets(): void {
		self::add_script( 'editor' );
	}
}
