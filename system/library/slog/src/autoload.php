<?php

/**
 * Autoloader to be used in the absence of the Composer autoloader
 */
$loader = new SplClassLoader('Slog', __DIR__);
$loader->register();