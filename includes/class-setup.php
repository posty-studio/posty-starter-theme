<?php

namespace Posty_Starter_Theme;

class Setup {
    private function set_constants() {
        define('POSTY_STARTER_THEME_VERSION', '1.0.0');
        define('POSTY_STARTER_THEME_SLUG', 'posty-starter-theme');
        define('POSTY_STARTER_THEME_PATH', plugin_dir_path(__DIR__));
        define('POSTY_STARTER_THEME_ASSETS_PATH', POSTY_STARTER_THEME_PATH . 'assets/');
        define('POSTY_STARTER_THEME_TEMPLATES_PATH', POSTY_STARTER_THEME_PATH . 'templates/');
        define('POSTY_STARTER_THEME_LANGUAGES_PATH', POSTY_STARTER_THEME_PATH . 'languages/');
        define('POSTY_STARTER_THEME_ASSETS_URL', plugin_dir_url(__DIR__) . 'assets/');
    }

    public function init() {
        $this->set_constants();
    }
}
