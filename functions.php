<?php

spl_autoload_register(function ($class) {
    if (strpos($class, 'Posty_Starter_Theme\\') !== 0) {
        return;
    }

    $file = str_replace('Posty_Starter_Theme\\', '', $class);
    $file = strtolower($file);
    $file = str_replace('_', '-', $file);

    /* Convert sub-namespaces into directories */
    $path = explode('\\', $file);
    $file = array_pop($path);
    $path = implode('/', $path);

    require_once __DIR__ . '/includes/' . $path . '/class-' . $file . '.php';
});

$setup = new Posty_Starter_Theme\Setup();
$setup->init();
