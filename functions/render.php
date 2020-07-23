<?php

namespace Posty;

/**
 * Render a partial.
 *
 * @param string $name
 * @param array $args
 */
function render(string $name, array $args = []) {
    echo get_template_part('partials/' . $name, null, $args);
}
