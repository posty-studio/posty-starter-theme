<?php

namespace Posty;

class Block_Editor {
    public static function init() {
        add_action('after_setup_theme', [__CLASS__, 'block_editor_support']);
    }

    public static function block_editor_support() {
        add_theme_support('align-wide');
        add_theme_support('disable-custom-colors');
        add_theme_support('responsive-embeds');
        add_theme_support('disable-custom-font-sizes');
        // add_theme_support('editor-color-palette', []);
        // add_theme_support('editor-font-sizes', []);
    },
}
