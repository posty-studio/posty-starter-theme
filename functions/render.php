<?php

namespace Posty;

/**
 * Render a partial.
 *
 * @param string $name
 * @param array $args
 * @return string
 */
function render(string $name, array $args = []) {
    ob_start();

    get_template_part('partials/' . $name, null, $args);

    return ob_get_clean();
}
