<?php

namespace Posty;

class Assets {
    public static function register() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_filter('style_loader_src', [__CLASS__, 'remove_ver_query_arg'], 10, 2);
    }

    /**
     * Remove version from styles.
     *
     * @param string $src
     * @return string
     */
    public static function remove_ver_query_arg(string $src) : string {
        return remove_query_arg('ver', $src);
    }

    /**
     * Get the hashed filename of a CSS file.
     *
     * @param string $name
     * @return string
     */
    private static function get_css_filename(string $name) : string {
        $map = POSTY_THEME_PATH . '/assets/css/manifest.json';
        $manifest = file_exists($map) ? json_decode(file_get_contents($map), true) : [];

        return $manifest[$name] ?? $name;
    }

    /**
     * Registers and enqueues a style.
     *
     * @param string $name
     * @param array $dependencies
     */
    private static function add_style(string $name, array $dependencies = []) {
        wp_enqueue_style(
            "posty-{$name}-style",
            POSTY_THEME_ASSETS_URL . '/css/' . self::get_css_filename($name . '.css'),
            $dependencies
        );
    }

    /**
     * Registers and enqueues a script.
     *
     * @param string $name
     * @param array $l10n
     * @param array $dependencies
     */
    private static function add_script(string $name, array $l10n = [], array $dependencies = []) {
        $asset_filepath = POSTY_THEME_ASSETS_PATH . '/js/' . $name . '.asset.php';
        $asset_file = file_exists($asset_filepath) ? include $asset_filepath : [
            'dependencies' => [],
            'version'      => POSTY_THEME_VERSION,
        ];

        wp_register_script(
            "posty-{$name}-script",
            POSTY_THEME_ASSETS_URL . '/js/' . $name . '.js',
            array_merge($asset_file['dependencies'], $dependencies),
            $asset_file['version'],
            true
        );

        if (!empty($l10n) && is_array($l10n)) {
            wp_localize_script("posty-{$name}-script", 'posty', $l10n);
        }

        wp_enqueue_script("posty-{$name}-script");
    }

    public static function enqueue_assets() {
        self::add_style('style');
        self::add_script('app');
    }
}
