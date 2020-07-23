<?php

namespace Posty;

class Setup {
    private function set_constants() {
        define('POSTY_THEME_VERSION', '1.0.0');
        define('POSTY_THEME_SLUG', 'posty-starter-theme');
        define('POSTY_THEME_PATH', get_template_directory());
        define('POSTY_THEME_URL', get_template_directory_uri());
        define('POSTY_THEME_ASSETS_PATH', POSTY_THEME_PATH . '/assets');
        define('POSTY_THEME_ASSETS_URL', POSTY_THEME_URL . '/assets');
        define('POSTY_THEME_TEMPLATES_PATH', POSTY_THEME_PATH . '/templates');
        define('POSTY_THEME_LANGUAGES_PATH', POSTY_THEME_PATH . '/languages');
    }

    public function init() {
        $this->set_constants();

        Assets::register();
        Block_Editor::init();
        Cleanup::init();
    }
}
